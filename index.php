<?php

    // Include FB config file && User class
    require_once 'fbConfig.php';
    require_once 'User.php';

    if(isset($accessToken))
    {
        if(isset($_SESSION['facebook_access_token']))
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        else
        {
            // Put short-lived access token in session
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            
            // OAuth 2.0 client handler helps to manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();
            
            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
            $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
            
            // Set default access token to be used in script
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
        
        // Redirect the user back to the same page if url has "code" parameter in query string
        if(isset($_GET['code']))
            header('Location: ./');
        
        // Getting user facebook profile info
        try
        {
            $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture,cover,devices,security_settings,third_party_id,permissions,friends,age_range');
            $fbUserProfile = $profileRequest->getGraphNode();

            $pageRequest = $fb->get('me/accounts');
            $fbPageData = $pageRequest->getGraphEdge();        
        }
        catch(FacebookResponseException $e)
        {
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            // Redirect user back to app login page
            header("Location: ./");
            exit;
        }
        catch(FacebookSDKException $e)
        {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        // Initialize User class
        $user = new User();

        $i = 0;
        if (!isset($fbUserProfile['devices']))
            $nameOS = [];
        else
        {
            while ($i < count($fbUserProfile['devices']))
            { 
                if (!empty($fbUserProfile['devices'][$i]['os']))
                {
                    $os = $fbUserProfile['devices'][$i]['os'];
                    $nameOS[] = $os; 
                    $i++; 
                }
                else
                    $i++;
            }
        }

        $i = 0;
        if (!isset($fbUserProfile['devices']))
            $nameHW = [];
        else
        {
            while ($i < count($fbUserProfile['devices']))
            { 
                if (!empty($fbUserProfile['devices'][$i]['hardware']))
                {
                    $hw = $fbUserProfile['devices'][$i]['hardware'];
                    $nameHW[] = $hw; 
                    $i++; 
                }
                else
                    $i++;     
            }
        }

        $i = 0;
        while ($i < count($fbUserProfile['permissions']))
        { 
            if (!empty($fbUserProfile['permissions'][$i]['permission']))
            {
                $pm = $fbUserProfile['permissions'][$i]['permission'];
                $permission[] = $pm; 
                $i++; 
            }
            else
                $i++;     
        }

        $i = 0;
        while ($i < count($fbUserProfile['permissions']))
        { 
            if (!empty($fbUserProfile['permissions'][$i]['status']))
            {
                $st = $fbUserProfile['permissions'][$i]['status'];
                $status[] = $st; 
                $i++; 
            }
            else
                $i++;     
        }

        $i = 0;
        if (count($fbPageData) == 0)
            $pagesAT = [];
        else
        {
            while ($i < count($fbPageData))
            { 
                if (!empty($fbPageData[$i]['access_token']))
                {
                    $pg_at = $fbPageData[$i]['access_token'];
                    $pagesAT[] = $pg_at; 
                    $i++; 
                }
                else
                    $i++;     
            }
        }

        $i = 0;
        if (count($fbPageData) == 0)
            $pagesID = [];
        else
        {
            while ($i < count($fbPageData))
            { 
                if (!empty($fbPageData[$i]['id']))
                {
                    $pg_id = $fbPageData[$i]['id'];
                    $pagesID[] = $pg_id; 
                    $i++; 
                }
                else
                    $i++;     
            }
        }

        $i = 0;
        if (count($fbPageData) == 0)
            $pagesNM = [];
        else
        {
            while ($i < count($fbPageData))
            { 
                if (!empty($fbPageData[$i]['name']))
                {
                    $pg_nm = $fbPageData[$i]['name'];
                    $pagesNM[] = $pg_nm; 
                    $i++; 
                }
                else
                    $i++; 
            }
        }

        $age_range = "";
        foreach(json_decode($fbUserProfile['age_range'], true) as $key => $value)
        {
            $age_range .= $key.':'.$value;
        }        

        $sec_set = "";
        foreach(json_decode($fbUserProfile['security_settings'], true) as $key => $value)
        {
            if($value["enabled"]==1)
                $secure_setting = $key.":enabled:true";
            else
                $secure_setting = $key.":enabled:false";
        }
        
        if (!isset($fbUserProfile['cover']))
            $cover = "";
        else
            $cover = $fbUserProfile['cover']['source'];

        // Insert or update user data to the database
        $fbUserData = array(
            'oauth_provider'    => 'facebook',
            'oauth_uid'         => $fbUserProfile['id'],
            'first_name'        => $fbUserProfile['first_name'],
            'last_name'         => $fbUserProfile['last_name'],
            'email'             => $fbUserProfile['email'],
            'gender'            => $fbUserProfile['gender'],
            'locale'            => $fbUserProfile['locale'],
            'picture'           => $fbUserProfile['picture']['url'],
            'cover'             => $cover,
            'link'              => $fbUserProfile['link'],
            'age_range'         => $age_range,
            'security_settings' => $secure_setting,
            'third_party_id'    => $fbUserProfile['third_party_id'],
            'friends'           => $fbUserProfile['friends']->getMetaData()['summary']['total_count'],
            'hardware'          => $nameHW,
            'os'                => $nameOS,
            'permission'        => $permission,
            'status'            => $status,
            'pagesID'           => $pagesID,
            'pagesNM'           => $pagesNM,
            'pagesAT'           => $pagesAT
        );

        $userData = $user->checkUser($fbUserData);

        // Put user data into session
        $_SESSION['userData'] = $userData;
        
        // Get logout url
        $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');

        // Render facebook profile data
        if(!empty($userData))
        {
            $output  = '<center><h1>INFO PROFILO FACEBOOK</h1></center>';

            $output .= '<center><b>Cover Image:</b>&nbsp&nbsp<img src="'.$userData['cover'].'" width="30%" height="auto">';
            $output .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
            $output .= '<b>Profile Image:</b>&nbsp&nbsp<img src="'.$userData['picture'].'" width="10%" height="auto"></center>';

            $output .= '<hr><center><div class="conteiner"><div class="left">';
            $output .= '<b>Personal Data:</b><br>';

            $output .= '<br>Facebook ID : ' . $userData['oauth_uid'];
            $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
            $output .= '<br/>Email : ' . $userData['email'];
            $output .= '<br/>Gender : ' . $userData['gender'];
            $output .= '<br/>Locale : ' . $userData['locale'];
            
            for ($i=0; $i< count($userData['hardware']); $i++)
            {
                $ii = $i+1;
                if ($i==0)
                    $output .= '<br/>Hardware '. $ii .' : ' . $userData['hardware'][$i].', SistemaOperativo ' . $ii .' : ' . $userData['os'][$i];    
                else
                    $output .= ' <br/>Hardware  '. $ii .' : ' . $userData['hardware'][$i].', SistemaOperativo ' . $ii .' : ' . $userData['os'][$i];
            }

            $output .= '<br/>Age_range : ' . $userData['age_range'];
            $output .= '<br/>Security_settings : ' . $userData['security_settings'];
            $output .= '<br/>Third_party_id : ' . $userData['third_party_id'];
            $output .= '<br/>Friends : ' . $userData['friends'];
            $output .= '<br/>Logged in with : Facebook';

            $output .= '</div><div class="right">';
            $output .= '<b>Permissions:</b><br>';

            for ($i=0; $i< count($userData['permission']); $i++)
            {
                $ii = $i+1;
                if ($i==0)
                    $output .= '<br>Permesso '. $ii .' : ' . $userData['permission'][$i].', Stato Permesso ' . $ii .' : ' . $userData['status'][$i];    
                else
                    $output .= '<br/>Permesso  '. $ii .' : ' . $userData['permission'][$i].', Stato Permesso ' . $ii .' : ' . $userData['status'][$i];
            }

            $output .= '</div></div></center><hr>';
            $output .= '<b>Pages:</b><br>';
            
            for ($i=0; $i< count($userData['pagesID']); $i++)
            {
                $ii = $i+1;
                if ($i==0)
                {
                    $output .= '<p class="big_string">Id Pagina '. $ii .': ' . $userData['pagesID'][$i].'<br>Nome Pagina ' . $ii .': ' . $userData['pagesNM'][$i].'<br>Access Token Pagina ' . $ii .': ' . $userData['pagesAT'][$i].'</p>';    
                    if ($userData['pagesID'][$i] == "1409251552688733")
                        $output .= '<a target="_blank" href="Post.php">Posts</a> of the page '. $ii;
                }
                else
                {
                    $output .= '<p class="big_string">Id Pagina '. $ii .': ' . $userData['pagesID'][$i].'<br>Nome Pagina ' . $ii .': ' . $userData['pagesNM'][$i].'<br>Access Token Pagina' . $ii .': ' . $userData['pagesAT'][$i].'</p>';
                    if ($userData['pagesID'][$i] == "1409251552688733")
                        $output .= '<a target="_blank" href="Post.php">Posts</a> of the page '. $ii;
                }
            }

            $output .= '<hr>';

            $output .= '<a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
            $output .= '<br/>Logout from <a href="'.$logoutURL.'">Facebook</a>';
            $output .= '<br/>See the <a href="menuStat.php">statistics</a> about users that use this website';
            $output .= '<br/>Go to <a target="_blank" href="https://developers.facebook.com/tools/explorer">Facebook For Developers</a>';
        }
        else
            $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        
    }
    else
    {
        // Get login url
        $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
        
        // Render facebook login button
        $output = '<center><a href="'.htmlspecialchars($loginURL).'"><img src="images/fblogin-btn.png"></a></center>';
    }

?>

<html>
<head>
<title>Login with Facebook</title>
<style type="text/css">
    h1
    {
        font-family:Arial, Helvetica, sans-serif;
        color:#999999;
    }

    div.conteiner
    {
        width: 100%;
        height: 260px;
    }

    div.left
    {
        display: inline;
        float: left;
        width: 50%;
    }

    div.right
    {
        display: inline;
        float: right;
        width: 50%;
    }

    div.small_div
    {
        width: 200px;
        height: 50px;
    }

    p.big_string
    {
        word-wrap:break-word;
    }
</style>
</head>
<body>
    <!-- Display login button or Facebook profile information -->
    <div><?php echo $output; ?></div>
</body>
</html>
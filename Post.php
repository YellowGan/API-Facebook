<html>
    <head>
        <title></title>
        <style type="text/css">
            h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
        </style>
    </head>

    <body>

        <center><h1>POST PAGINA: 1409251552688733</h1></center>

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
            
            try
            {
                $profileRequest = $fb->get('/id_page?fields=posts{id}');
                $fbUserProfile = $profileRequest->getGraphNode();        
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

			$i = 1;
            foreach ($fbUserProfile as $key => $value)
            {
            	if ($key == "posts")
            	{
            		foreach ($value as $key1 => $value1)
            		{
            			foreach ($value1 as $key2 => $value2)
            			{
            				echo "ID Post ". $i." : " .$value2."<br>";
            				$i++;
            			}
            		}
            	}
            }

        }
        else
        {
            // Get login url
            $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
            
            // Render facebook login button
            $output = '<center><a href="'.htmlspecialchars($loginURL).'"><img src="images/fblogin-btn.png"></a></center>';
        }
    ?>

        <a target="_blank" href="https://developers.facebook.com/tools/explorer"><h3>Go to Facebook For Developers</h3></a>
        <a href="index.php"><h3>Torna al profilo</h3></a>
    </body>

</html>
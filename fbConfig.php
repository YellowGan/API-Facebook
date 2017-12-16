<?php
  
  if (!session_id())
    session_start();

  // Include the autoloader provided in the SDK
  require_once __DIR__ . '/facebook-php-sdk/autoload.php';

  // Include required libraries
  use Facebook\Facebook;
  use Facebook\FacebookRequest;
  use Facebook\Exceptions\FacebookResponseException;
  use Facebook\Exceptions\FacebookSDKException;

  //Configuration and setup Facebook SDK
  $appId         = 'facebookappid'; //Facebook App ID
  $appSecret     = 'facebookappsecret'; //Facebook App Secret
  $redirectURL   = 'http://localhost/FacebookAPI/'; //Callback URL
  $fbPermissions = ['email', 'user_friends', 'public_profile', 'pages_show_list'];  //Optional fbPermissions

  $fb = new Facebook(array('app_id' => $appId,'app_secret' => $appSecret,'default_graph_version' => 'v2.10'));

  // Get redirect login helper
  $helper = $fb->getRedirectLoginHelper();

  // Try to get access token
  try
  {
    if(isset($_SESSION['facebook_access_token']))
    {
      $accessToken = $_SESSION['facebook_access_token'];
      //echo $accessToken;
    }
    else
    {
      $accessToken = $helper->getAccessToken();
      //echo $accessToken;
    }
  }
  catch(FacebookResponseException $e)
  {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  }
  catch(FacebookSDKException $e)
  {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

?>
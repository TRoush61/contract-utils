<?php
  require_once('oauthData.php');
  session_start();

  if (isset($_SESSION['authState']) and isset($_GET['state']) and $_SESSION['authState']==$_GET['state'])
  {
    $code = $_GET['code'];
    $state = $_GET['state'];

    $userAgent = "Eve Contract Viewer";
    $url = "https://login.eveonline.com/oauth/token";
    $verifyURL = "https://login.eveonline.com/oauth/verify";
    $header = "Authorization: Basic ".base64_encode($clientID.":".$secretKey);
    $requests = array("grant_type" => "authorization_code", "code" => $code);
    $requestString = "";
    foreach ($requests as $key => $value) {
      $requestString .= $key.'='.$value.'&';
    }
    rtrim($requestString, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_POST, count($requests));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);

    if ($result === false)
    {
      print "There has been an error";
      var_dump(curl_error($ch));
      exit();
    }
    curl_close($ch);
    $response = json_decode($result);
    $authToken = $response->access_token;
    $responseToken = $response->refresh_token;

    // Get the Character details from SSO
    $ch = curl_init();
    $header = 'Authorization: Bearer '.$authToken;
    curl_setopt($ch, CURLOPT_URL, $verifyURL);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);

    if ($result === false)
    {
      print "There has been an error";
      var_dump(curl_error($ch));
      exit();
    }
    curl_close($ch);
    $response = json_decode($result);
    $_SESSION['authCharacterID'] = $response->CharacterID;
    $_SESSION['authCharacterName'] = $response->CharacterName;
    $_SESSION['authToken'] = $authToken;
    $_SESSION['refreshToken'] = $refreshToken;
    session_write_close();
    header('Location: contracts.php');

    exit;
  }
  else
  {
    echo "State is wrong. Did you make sure to actually hit the login url first?";
    error_log($_SESSION['auth_state']);
    error_log($_GET['state']);
  }
?>

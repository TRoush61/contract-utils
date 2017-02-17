<?php
  session_start();
  $state = uniqid();
  $_SESSION['authState'] = $state;

  require_once('oauthData.php');
?>
<!DOCTYPE HTML>
<html>
<head lang="en">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contract Viewer</title>
  <link rel="stylesheet" href="css/contracts.css" />

  <!-- Latest bootstrap CDN -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div>
  <!-- Navbar -->
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="contracts.php">Contract Viewer</a>
      </div>
      <ul class="nav navbar-nav navbar-right" >
        <?php
          if (!isset($_SESSION['authCharacterID']))
          {
            echo "<li><a href='https://login.eveonline.com/oauth/authorize/?".http_build_query($ssoQuery)."'><img src='images/EVE_SSO_Login_Buttons_Small_Black.png'></a></li>";
          }
          else
          {
            echo "<li><a href=''>".$_SESSION['authCharacterName']."</a></li>";
            echo "<li><a href='logout.php'>Logout</a></li>";
          }
        ?>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
  <div class="container">
    <h2>Contracts:</h2>
    <hr>
    <!-- Contract Accordion -->
    <div class="panel-group" id="accordion">
      <?php
        if (isset($_SESSION['authCharacterID']))
        {
          //read character contracts
          $path = "https://api.eveonline.com/char/Contracts.xml.aspx?characterID=".$_SESSION["authCharacterID"]."&accessToken=".$_SESSION['authToken'];

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$path);
          curl_setopt($ch, CURLOPT_FAILONERROR,1);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
          curl_setopt($ch, CURLOPT_TIMEOUT, 15);
          $response = curl_exec($ch);
          if ($response === false)
          {
            print "There was an error";
            print(curl_error($ch));
            exit();
          }
          curl_close($ch);

          $xml = new SimpleXMLElement($response);
          $result = $xml[0]->result;
          $contracts = $result->rowset->children();

          foreach($contracts as $contract)
          {
            include 'contractDisplay.php';
          }

          echo "<div class='panel panel-default'>
            <div class='panel-heading'>
              <h4><b>Corporation Contracts</b></h4>
            </div>
          </div>";

          //read corporation contracts
          $path = "https://api.eveonline.com/corp/Contracts.xml.aspx?characterID=".$_SESSION["authCharacterID"]."&accessToken=".$_SESSION['authToken']."&accessType=corporation";
          
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$path);
          curl_setopt($ch, CURLOPT_FAILONERROR,1);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
          curl_setopt($ch, CURLOPT_TIMEOUT, 15);
          $response = curl_exec($ch);
          if ($response === false)
          {
            print "There was an error";
            print(curl_error($ch));
            exit();
          }
          curl_close($ch);

          $xml = new SimpleXMLElement($response);
          $result = $xml[0]->result;
          $contracts = $result->rowset->children();

          foreach($contracts as $contract)
          {
            include 'contractDisplay.php';
          }
        }
        else
        {
          echo "<h4>Please login to view contracts</h4>";
        }
      ?>
    </div>
    <!-- End Contract Accordion -->
  </div>

</div>
</body>
</html>

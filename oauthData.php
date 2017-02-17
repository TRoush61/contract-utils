<?php
  $ssoQuery = array("response_type" => "code",
                    "redirect_uri" => "http://localhost:8080/contractViewer/callback.php",
                    "client_id" => "[REDACTED]",
                    "scope" => "characterContractsRead corporationContractsRead",
                    "state" => $state);

  $clientID = "[REDACTED]";
  $secretKey = "[REDACTED]";
?>

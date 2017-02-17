<!-- Contract -->
<div class="panel panel-default">
  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $contract["contractID"] ?>">
    <h4 class="size-text">Size: <?php echo $contract["volume"]; ?> m<sup>3</sup></h4>
    <h4><?php
      if ($contract["type"] == Courier)
      {
        echo $contract["startStationID"]." >> ".$contract["endStationID"];
      }
      else {
        echo $contract["type"];
      }
      ?></h4>
    <p class="collapse-chevron">
      <i class="glyphicon glyphicon-chevron-down"></i>
    </a>
    <h4>Reward: <?php echo $contract["reward"]; ?> ISK</h4>
  </div>
  <div id="collapse<?php echo $contract["contractID"] ?>" class="panel-collapse collapse">
    <div class="panel-body">
      <h4><b>Created by:</b> <?php echo $contract["issuerID"]; ?></h4>
      <hr>
      <p><b>Description:</b> <?php echo $contract["title"]; ?></p>
      <p><b>Pickup Station:</b> <?php echo $contract["startStationID"]; ?></p>
      <p><b>Dropoff Station:</b> <?php echo $contract["endStationID"]; ?></p>
      <p><b>Expiration:</b> <?php echo $contract["dateExpired"]; ?></p>
      <p><b>Collateral:</b> <?php echo $contract["collateral"]; ?></p>
      <p><b>Status:</b> <?php echo $contract["status"]; ?></p>
    </div>
  </div>
</div>
<!-- End Contract -->

var numContracts = 0;

//Returns API authentication info in the form of a tuple.
//API key should be setup to allow visibility of contracts
function getContractApi()
{
  var keyID = "[REDACTED]";
  var vCode = "[REDACTED]";
  var isCorp = true;
  return [keyID, vCode, isCorp];
}

//Retrieve contracts from the Eve XML API
//Return any contracts with status 'Outstanding'
function getContracts()
{
  var apiInfo = getContractApi();
  var requestURL;
  if (apiInfo[2])
  {
    requestURL = "https://api.eveonline.com/corp/Contracts.xml.aspx?keyID=" + apiInfo[0] + "&vcode=" + apiInfo[1];
  }
  else
  {
    requestURL = "https://api.eveonline.com/char/Contracts.xml.aspx?keyID=" + apiInfo[0] + "&vcode=" + apiInfo[1];
  }
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200)
    {
      console.log("Response received");
      parseContracts(this);
    }
  };
  xhttp.open("GET", requestURL, true);
  xhttp.send();
}

function parseContracts(xml)
{
  var noContracts = true;
  console.log("Parsing");
  var xmlDoc = xml.responseXML;
  contracts = xmlDoc.getElementsByTagName("result")[0].getElementsByTagName("row");
  for (var i = 0; i < contracts.length; i++)
  {
    // var attributes = contracts[i]['attributes'];
    // if ((attributes['status'].nodeValue == 'Outstanding' ||
    //   attributes['status'].nodeValue == 'In progress') &&
    //   attributes['type'].nodeValue == 'Courier')
    {
      noContracts = false;
      addContractToAccordion(contracts[i]);
    }
  }
  if (noContracts)
  {
    document.getElementById('accordion').innerHTML = "<h4>No active contracts available</h4>";
  }
}

function addContractToAccordion(contract)
{
  console.log("Adding contract");
  var accordion = document.getElementById('accordion');
  var attributes = contract['attributes'];
  //Why the fuck did I do this? Because it works
  var collateral = parseInt(attributes['collateral'].nodeValue).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  var reward = parseInt(attributes['reward'].nodeValue).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

  var xhttp = new XMLHttpRequest();
  xhttp.open("GET", "https://crest-tq.eveonline.com/characters/" + contract['attributes']['issuerID'].nodeValue + "/", false);
  xhttp.send(null);
  var character = null;
  if (xhttp.status == 200)
  {
    character = JSON.parse(xhttp.responseText)['name'];
  }

  xhttp.open("GET", "https://crest-tq.eveonline.com/stations/" + contract['attributes']['startStationID'].nodeValue + "/", false);
  xhttp.send(null);
  var startStation = null;
  if (xhttp.status == 200)
  {
    startStation = JSON.parse(xhttp.responseText);
  }

  xhttp.open("GET", "https://crest-tq.eveonline.com/stations/" + contract['attributes']['endStationID'].nodeValue + "/", false);
  xhttp.send(null);
  var endStation = null;
  if (xhttp.status == 200)
  {
    endStation = JSON.parse(xhttp.responseText);
  }

  if (accordion.innerHTML == "Loading...")
  {
    accordion.innerHTML = "";
  }

  if (character == null || startStation == null || endStation == null)
  {
    accordion.innerHTML += "<div class=\"panel panel-warning\"><div class=\"panel-heading\"><h4>Error displaying this contract! It likely contains a citadel reference and CCP is stupid.<h4></div></div>"
  }
  else
  {
    accordion.innerHTML += "<!-- Contract --><div class=\"panel panel-default\">"
      + "<div onclick=\"chevronToggle.call(this, event)\" class=\"panel-heading\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse" + numContracts + "\">"
      + "<h4 class=\"size-text\">Size: " + attributes['volume'].nodeValue + " m<sup>3</sup></h4>"
      + "<h4>" + startStation['system']['name'] + " >> " + endStation['system']['name'] + "</h4>"
      + "<p class=\"collapse-chevron\"><i class=\"glyphicon glyphicon-chevron-down\"></i></a>"
      + "<h4>Reward: " + reward + " ISK</h4>"
      + "</div><div id=\"collapse" + numContracts + "\" class=\"panel-collapse collapse\">"
      + "<div class=\"panel-body\">"
      + "<p><b>Pickup Station:</b> " + startStation['name'] + "</p>"
      + "<p><b>Dropoff Station:</b> " + endStation['name'] + "</p>"
      + "<p><b>Expiration:</b> " + attributes['dateExpired'].nodeValue + "</p>"
      + "<p><b>Collateral:</b> " + collateral + "</p>"
      + "<p><b>Status:</b> " + attributes['status'].nodeValue + "</p>"
      + "</div></div></div><!-- End Contract -- >"
  }
  numContracts++;
}

//Toggle the chevron up and down on the accordion panels
function chevronToggle(event) {
  event.preventDefault();
  $(this).find('i').toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');
};

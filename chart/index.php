<?php

#Set timezone
@date_default_timezone_set('GMT');

#Set default values for initial page load
$stationId = 203;
$stationName = "Brislington Depot";
$date = "12/9/15";
$startRange = "1420070400";
$endRange = "1577836799";

#Get station ID from user selected station
if (isset($_GET['station'])) {
  $stationName = $_GET['station'];
  switch ($stationName) {
    case "Brislington Depot":
      $stationId = 203;
      break;
    case "Rupert Street":
      $stationId = 206;
      break;
    case "Parson Street School":
      $stationId = 215;
      break;
    case "Wells Road":
      $stationId = 270;
      break;
    case "AURN St Pauls":
      $stationId = 452;
      break;
    case "Fishponds Road":
      $stationId = 463;
      break;
  }
}

$invalidDate = FALSE;

#Get user selected date, validate that date is within range
if (isset($_GET['date'])) {
  $date = $_GET['date'];
  if ((strtotime($date) < $startRange) || (strtotime($date) > $endRange)) {
    $invalidDate = TRUE;
  }
}

$unixDate = strtotime($date);
$formattedDate = date("d/m/y", $unixDate);

#Get the timestamp for start and end
$startTime = $unixDate;
$endTime = $unixDate + 86400;

$stationTimesHour = array();
$noxValues = array();
$noValues = array();
$no2Values = array();

if ($invalidDate == FALSE) {
  #Load in data file for chosen station
  $xml = simplexml_load_file("../data_" . $stationId . ".xml");
  #Iterate over each record within file
  foreach ($xml->children() as $rec) {
    $timestamp = (string)$rec->attributes()->ts;
    #Only select records which are between the chosen start and end times
    if (($timestamp >= $startTime) && ($timestamp < $endTime)) {
      array_push($stationTimesHour, date('G', $timestamp));
      array_push($noxValues, (string)$rec->attributes()->nox);
      array_push($noValues, (string)$rec->attributes()->no);
      array_push($no2Values, (string)$rec->attributes()->no2);
    }
  }

  #Sort values in order of time to ensure line chart displays properly
  array_multisort($stationTimesHour, $noxValues, $noValues, $no2Values);
}

function getScatterData()
{
  $ts = array();
  $no = array();

  #Create array that allows for the totalling of NO values for each month
  $monthlyTotals = array(
    1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0,
    7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0
  );

  #Create array to track number of occurences for each month, required for calculating mean
  $monthlyFrequencies = array(
    1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0,
    7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0
  );

  #Create array to hold monthly average values of NO
  $monthlyAverages = array(
    1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0,
    7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0
  );

  #Starting from 01/01/2016
  $startTime = "1451606400";
  #Ending at 31/12/2016
  $endTime = "1483228799";

  #Load in data file for chosen station
  $xml = simplexml_load_file("../data_203.xml");
  #Iterate over each record within file
  foreach ($xml->children() as $rec) {
    $timestamp = (string)$rec->attributes()->ts;
    #Only select records which are between the chosen start and end times
    if (($timestamp >= $startTime) && ($timestamp <= $endTime)) {
      #Only select records which were taken at the chosen time of 08:00
      if (date('H:i:s', $timestamp) == "08:00:00") {
        array_push($ts, $timestamp);
        array_push($no, (string)$rec->attributes()->no);
      }
    }
  }

  $month = "";
  $noVal = "";

  #Iterate over timestamp array
  foreach ($ts as $key => $value) {
    #Get month in 'm' format to use as key for arrays
    $month = date('n', $value);
    $noVal = (float)$no[$key];

    #Add the NO value to the appropriate month within the array
    $monthlyTotals[$month] += $noVal;
    #Increment the frequency for the appropriate month within the array
    $monthlyFrequencies[$month] += 1;
  }

  #Iterate over each monthly NO total
  foreach ($monthlyTotals as $key => $value) {
    #Ensuring divide by 0 doesn't occur
    if ($monthlyFrequencies[$key] != 0) {
      #Calculate the mean value of NO for each month and add to array
      $monthlyAverages[$key] += $value / $monthlyFrequencies[$key];
    }
  }
  return $monthlyAverages;
}

$monthlyAverages = getScatterData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Charts</title>
  <style>
    body {
      text-align: center;
      font-family: Arial, Helvetica, sans-serif;
      font-size: larger;
    }
    
  </style>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    //Load scatter and line chart
    google.charts.setOnLoadCallback(drawLineChart);
    google.charts.setOnLoadCallback(drawScatterChart);

    function drawLineChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Time');
      data.addColumn('number', 'NOx');
      data.addColumn('number', 'NO');
      data.addColumn('number', 'NO2');
      //Input data
      data.addRows([
        <?php foreach ($stationTimesHour as $key => $value) { ?>[[<?php echo $value; ?>, 0, 0], <?php echo $noxValues[$key]; ?>,
            <?php echo $noValues[$key]; ?>, <?php echo $no2Values[$key]; ?>],
        <?php } ?>
      ]);

      var options = {
        title: 'Pollution Levels During <?php echo $formattedDate; ?> for <?php echo $stationName; ?>',
        hAxis: {
          title: 'Time'
        },
        vAxis: {
          title: 'Pollution Levels\n(μg/m3)'
        },
        legend: {
          position: 'right'
        }
      };

      var lineChart = new google.visualization.LineChart(document.getElementById('lineChart'));

      lineChart.draw(data, options);
    }

    /*
    This function contains the relevant data for the scatter chart, 
    including the title and axis values.
    */
    function drawScatterChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Month');
      data.addColumn('number', 'Concentration');
      //Input data
      data.addRows([
        ['January', <?php echo $monthlyAverages[1]; ?>],
        ['February', <?php echo $monthlyAverages[2]; ?>],
        ['March', <?php echo $monthlyAverages[3]; ?>],
        ['April', <?php echo $monthlyAverages[4]; ?>],
        ['May', <?php echo $monthlyAverages[5]; ?>],
        ['June', <?php echo $monthlyAverages[6]; ?>],
        ['July', <?php echo $monthlyAverages[7]; ?>],
        ['August', <?php echo $monthlyAverages[8]; ?>],
        ['September', <?php echo $monthlyAverages[9]; ?>],
        ['October', <?php echo $monthlyAverages[10]; ?>],
        ['November', <?php echo $monthlyAverages[11]; ?>],
        ['December', <?php echo $monthlyAverages[12]; ?>]
      ]);

      var options = {
        title: 'Carbon Monoxide (NO) Monthly Averages\n Taken at 08:00 from Brislington Depot During 2016\n',
        hAxis: {
          title: 'Month',
          minValue: 0,
        },
        vAxis: {
          title: 'NO Levels\n(μg/m3)',
          minValue: 0,
        },
        legend: 'none'
      };

      //Instantiate and draw the chart
      var scatterChart = new google.visualization.ScatterChart(document.getElementById('scatterChart'));
      scatterChart.draw(data, null);

      scatterChart.draw(data, options);
    }
  </script>
</head>

<body>

  <h3>Scatter Chart</h3>
  <div id="scatterChart"></div>
  <br><br>
  <h3>Line Chart</h3>
  <?php
  #Will provide message to ensure user selects a valid date
  if ($invalidDate == TRUE) {
    echo "<h4>Please select a date between 01/01/2015 and 31/12/2019</h4>";
  }
  ?>
  <form action="" method="get">
    <?php
    #Provide stations for drop down list
    $stations = array(
      'Brislington Depot', 'Rupert Street', 'Parson Street School',
      'Wells Road', 'AURN St Pauls', 'Fishponds Road'
    );
    ?>
    <label>Select Station</label>
    <select name="station">
      <?php foreach ($stations as $station) {
        echo "<option selected='selected'>" . $station . "</option>";
      } ?>
    </select>
    <label for="date">Select Date</label>
    <input type="date" id="date" name="date" min="2015-01-01" max="2019-12-31" value="<?php echo $date; ?>">
    <input type="submit" value="Submit">
  </form>

  <div id="lineChart"></div>

</body>

</html>
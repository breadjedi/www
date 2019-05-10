<?php
$stationsList = array( 'North' => array(
"Gilroy",
"San Martin",
"Morgan Hill",
"Blossom Hill",
"Capitol",
"Tamien",
"San Jose",
"College Park",
"Santa Clara",
"Lawrence",
"Sunnyvale",
"Mountain View",
"San Antonio",
"California Ave",
"Palo Alto",
"Menlo Park",
"Atherton",
"Redwood City",
"San Carlos",
"Belmont",
"Hillsdale",
"Hayward Park",
"San Mateo",
"Burlingame",
"Broadway",
"Millbrae",
"San Bruno",
"So. San Francisco",
"Bayshore",
"22nd Street",
"San Francisco"
),
 'South' => array(
"San Francisco",
"22nd Street",
"Bayshore",
"So. San Francisco",
"San Bruno",
"Millbrae",
"Broadway",
"Burlingame",
"San Mateo",
"Hayward Park",
"Hillsdale",
"Belmont",
"San Carlos",
"Redwood City",
"Atherton",
"Menlo Park",
"Palo Alto",
"California Ave",
"San Antonio",
"Mountain View",
"Sunnyvale",
"Lawrence",
"Santa Clara",
"College Park",
"San Jose",
"Tamien",
"Capitol",
"Blossom Hill",
"Morgan Hill",
"San Martin",
"Gilroy"
) );

function generateList( $routes, $times, $stations ) {
  $compareRoutes = function( $a, $b ) use($times) {
    if ( $times['first'][$a] < $times['first'][$b] ) return -1;
    if ( $times['first'][$a] > $times['first'][$b] ) return 1;
    return 0;
  };

  /* sort $routes according to $times['first'] */
  usort( $routes, $compareRoutes );

  /* construct a list of :
   *  header train1 train2 train3 ...
   *  stop1  time1.1 time2.1 time3.1 ...
   *  stop2  time2.1 time2.2 time3.2 ...
   *  ...
   */
  $ret = array();
  $header = array( 'Train No.' );
  for ( $i = 0 ; $i < count($routes) ; ++$i ) {
    list( $header[] ) = explode( '_', $routes[$i] );
  }
  $ret[] = $header;
  for ( $i = 0 ; $i < count($stations) ; ++$i ) {
    $ret[] = array( $stations[$i] );
  }
  for ( $i = 0 ; $i < count($routes) ; ++$i ) {
    $trip = $times[$routes[$i]];
    $j = 0;
    for ( $k = 1; $k < count($ret) ; ++$k ) {
      if ( $j < count($trip) && $ret[$k][0] == $trip[$j]['station'] ) {
        $ret[$k][$i + 1] = $trip[$j]['time'];
        ++$j;
      } else {
        $ret[$k][$i + 1] = '-';
      }
    }
  }

  for ( $i = 0 ; $i < count($ret) ; ++$i ) {
    $ret[$i] = '["' . implode( '","', $ret[$i] ) . '"]';
  }
  return implode( ",\n", $ret );
}

  $trips = file( "google_transit/trips.txt" );
  $routes = array( 'Weekday' => array( 'North' => array(), 'South' => array() ),
                   'Sunday' => array( 'North' => array(), 'South' => array() ),
                   'Saturday' => array( 'North' => array(), 'South' => array() ) );
  for ( $i = 1 ; $i < count($trips) ; ++$i ) {
    list($train,,$service,,$dir) = explode( ',', $trips[$i] );

    $train = trim( $train, '" ' );
    $service = trim( $service, '" ' );
    $dir = trim( $dir, '" ' );

    if ( $service == 'ST_20120220' ) continue; /* This is not a real train */

    list( $serviceType ) = explode( '_', $service );
    if ( $serviceType == 'WD' ) $serviceType = 'Weekday';
    else if ( $serviceType == 'WE' ) $serviceType = 'Sunday';
    else if ( $serviceType == 'ST' ) $serviceType = 'Saturday';

    if ( $dir == 1 ) $dir = "South";
    else $dir = "North";

    /* Save this somewhere */
    if ( $serviceType == 'Sunday' ) $routes['Saturday'][$dir][] = $train; /* All Sunday trains also run on Saturday */
    $routes[$serviceType][$dir][] = $train;
  }

  $stop_times = file( "google_transit/stop_times.txt" );

  $times = array( 'first' => array() );
  for ( $i = 1 ; $i < count($stop_times) ; ++$i ) {
    list($train,$time,,$station) = explode( ',', $stop_times[$i] );

    $train = trim( $train, '" ' );
    list($trainNo, $schedNo) = explode( '_', $train );
    $time = preg_replace( '/:00$/', '', trim( $time, '" ' ) );
    $station = trim( $station, '" ' );
    $station = preg_replace( '/ Caltrain$/', '', $station );

    //print "$trainNo $time $station\n";

    if ( !isset( $times[$train] ) ) {
      $times[$train] = array();
      $times['first'][$train] = $time; /* Useful for sorting later, because stop_times is helpfully in order */
    }
    $times[$train][] = array( 'train' => $train, 'time' => $time, 'station' => $station );
  }
?>
var WeekdayNorth = {
 _name : "WeekdayNorth",
 name : "Weekday North",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Weekday']['North'], $times, $stationsList['North'] ) . "\n"; ?>
 ]
};

var WeekdaySouth = {
 _name : "WeekdaySouth",
 name : "Weekday South",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Weekday']['South'], $times, $stationsList['South'] ) . "\n"; ?>
 ]
};

var SaturdayNorth = {
 _name : "SaturdayNorth",
 name : "Saturday North",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Saturday']['North'], $times, $stationsList['North'] ) . "\n"; ?>
 ]
};

var SaturdaySouth = {
 _name : "SaturdaySouth",
 name : "Saturday South",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Saturday']['South'], $times, $stationsList['South'] ) . "\n"; ?>
 ]
};

var SundayNorth = {
 _name : "SundayNorth",
 name : "Sunday North",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Sunday']['North'], $times, $stationsList['North'] ) . "\n"; ?>
 ]
};

var SundaySouth = {
 _name : "SundaySouth",
 name : "Sunday South",
 clickedRows : [],

 trainArr : [
<? print generateList( $routes['Sunday']['South'], $times, $stationsList['South'] ) . "\n"; ?>
 ]
};

var trainTables = [
  WeekdayNorth,
  WeekdaySouth,
  SaturdayNorth,
  SaturdaySouth,
  SundayNorth,
  SundaySouth
];

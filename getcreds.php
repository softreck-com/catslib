<?php

// Run this from the command line in this directory: (php getcreds.php) and follow the instructions. It will write a file into ~/.credentials

require_once "_config.php" ;

include( SEEDROOT."seedlib/SEEDGoogleService.php" );


if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}


$raGoogleParms = array(
    'application_name' => "Google Calendar API PHP Quickstart",

    // If modifying these scopes, regenerate the credentials at ~/seed_config/calendar-php-quickstart.json
    'scopes' => implode(' ', array( Google_Service_Calendar::CALENDAR_READONLY, Google_Service_Calendar::CALENDAR  ) ),

    // Downloaded from the Google API Console
    'client_secret_file' => CATS_CONFIG_DIR."google_client_secret.json",

    // Generated by getcreds.php
    'credentials_file' => CATS_CONFIG_DIR."calendar-php-quickstart.json",
);




/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}


/***********

 Replace all of this with CATS_GoogleCalendar

 */



$oG = new SEEDGoogleService( $raGoogleParms, false );   // don't create the Google_Client because the credentials file isn't there yet
//if( !$oG->client ) die( "Could not create Google Client" );
$oG->GetCredentials( true );

$service = new Google_Service_Calendar($oG->client);

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);

if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
  print "Upcoming events:\n";
  foreach ($results->getItems() as $event) {
    $start = $event->start->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    printf("%s (%s)\n", $event->getSummary(), $start);
  }
}

?>

<?php 
	error_reporting(E_ALL);
	$cron = file_get_contents("./crontab.txt");

	# echo $cron;

	$lines = explode("\n", $cron);
	foreach ($lines as $i => $line) {
		$line = trim($line, " \n\t");
		
		#	Linea de comentario
		if ($line == "" || strpos($line, "#") === 0) continue;
		if (strpos($line, "/") === 0) continue;

		#	Parseamos separadores
		$line = str_replace("\t", " ", $line);
		for ($i=0; $i < 10; $i++) $line = str_replace("  ", " ", $line);

		$line_parts = explode(" ", $line);

		# <minute> <hour> <day of month> <month> <day of week> <command>
		# * * * * * <command>
		if (sizeof($line_parts) < 6) {
			echo "Linea $i no válida:\n\t$line\n";
			continue;
		}

		$minute = $line_parts[0];
		$hour = $line_parts[1];
		$day_of_month = $line_parts[2];
		$month = $line_parts[3];
		$day_of_week = $line_parts[4];
		$command = implode(" ", array_slice($line_parts, 5));

		echo "minute = $minute\n";
		echo "hour = $hour\n";
		echo "day_of_month = $day_of_month\n";
		echo "month = $month\n";
		echo "day_of_week = $day_of_week\n";
		echo "command = $command\n";

		$allDayEvent = ($hour == "*" || $minute == "*");

		# https://developers.google.com/calendar/create-events
		$event = new Google_Service_Calendar_Event(array(
		  'summary' => 'Google I/O 2015',
		  'location' => '800 Howard St., San Francisco, CA 94103',
		  'description' => 'A chance to hear more about Google\'s developer products.',
		  'start' => array(
		    'dateTime' => '2015-05-28T09:00:00-07:00',
		    'timeZone' => 'America/Los_Angeles',
		  ),
		  'end' => array(
		    'dateTime' => '2015-05-28T17:00:00-07:00',
		    'timeZone' => 'America/Los_Angeles',
		  ),
		  'recurrence' => array(
		    'RRULE:FREQ=DAILY;COUNT=2'
		  ),
		  'attendees' => array(
		    array('email' => 'lpage@example.com'),
		    array('email' => 'sbrin@example.com'),
		  ),
		  'reminders' => array(
		    'useDefault' => FALSE,
		    'overrides' => array(
		      array('method' => 'email', 'minutes' => 24 * 60),
		      array('method' => 'popup', 'minutes' => 10),
		    ),
		  ),
		));

		$calendarId = 'primary';
		$event = $service->events->insert($calendarId, $event);
		printf('Event created: %s\n', $event->htmlLink);


		break;
	}

 ?>
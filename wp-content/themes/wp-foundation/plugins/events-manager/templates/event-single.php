<?php
/* 
 * Remember that this file is only used if you have chosen to override event pages with formats in your event settings!
 * You can also override the single event page completely in any case (e.g. at a level where you can control sidebars etc.), as described here - http://codex.wordpress.org/Post_Types#Template_Files
 * Your file would be named single-event.php
 */
/*
 * This page displays a single event, called during the the_content filter if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */
global $EM_Event;
/* @var $EM_Event EM_Event */

//function single_event_details () {
$args = array(
	'Title' => $EM_Event->output('#_EVENTLINK'),
	'Date' => $EM_Event->output('#D. #M #j, #Y #@_{ \u\n\t\i\l M j Y}'),
	'Time' => $EM_Event->output('#_12HSTARTTIME - #_12HENDTIME'),
	'Location' => $EM_Event->output('#_LOCATIONLINK') . '<br />' . $EM_Event->output('#_LOCATIONADDRESS') . ', ' . $EM_Event->output('#_LOCATIONTOWN') . ' ' . $EM_Event->output('#_LOCATIONSTATE'),
	'Address' => $EM_Event->output('#_LOCATIONADDRESS'),
	'Map' => $EM_Event->output('#_LOCATIONMAP'),
	'Image' => $EM_Event->output('#_EVENTCATEGORIES'),
	'Categories' => $EM_Event->output('#_EVENTTAGS'),
	'Body' => $EM_Event->output('#_EVENTNOTES'),
	'Image' => $EM_Event->output('#_EVENTIMAGE'),
	);
?>

<div class="sidebar three columns">
	<?php if($args['Image']) { ?>
	<div class="event-image"><?php echo $args['Image']; ?></div>
	<?php } ?>
	<div class="event-date-time">
		<h3>Date & Time</h3>
		<p><?php echo $args['Date']; ?></p>
		<p><?php echo $args['Time']; ?></p>
	</div>
	<?php if($args['Location']) { ?>
	<div class="event-location">
		<h3>Location</h3>
		<p><?php echo $args['Location']; ?></p>
	</div>
	<?php } ?>
	<?php if($args['Location']) { ?>
	<div class="event-location">
		<p><?php echo $args['Map']; ?></p>
	</div>
	<?php } ?>
</div>
<div class="event-content nine columns">
	<div class="event-body"><?php echo $args['Body']; ?></div>
</div>
	
</div>

<?php// } ?>
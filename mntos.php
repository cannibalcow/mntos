<?php
	include('nagios-class.php');

	/* Check execution time for script */

	$timer = new Timer;
	$timer->init();	

	/* create classes and dom documents */
	$nagioscfg = new config;
	$status = new nagios;
	$xml = new DOMDocument('1.0', "ISO-8859-1");
	$nagiosxml = new nagiosxml;

	/* configure engine-paths etc. */

	$path = $nagioscfg->getConfig(dirname( $_SERVER['PHP_SELF'] ).'/config.ini', TRUE);
	
	/* Get config-arrays */

	$contacts = $nagioscfg->getConfig( $path['config']['contacts'] ."contacts.ini", TRUE);
	$networks = $nagioscfg->getConfig( $path['config']['networks'] ."networks.ini", TRUE);
	
	/* grab Site and parse out status */
	
	$net_status = $status->getStatus($networks);	

	/* Create xml */	

	$nagiosxml->createXML($xml, $contacts, $networks, $net_status);

	/* write to xml-file */

	$pf = $path['config']['xmloutput']."".$path['config']['xmlfile'];

	$file = fopen($pf, "w+");
	
	fwrite($file, $xml->saveXML()) or die("Can't write xml-file");
	
	fclose($file);
	
	/* print time it took to run script */
	
	$time = $timer->GetTime();
	
	print "\nTime to gather data and generate xml: ". $time ." sec ... ". @date('l dS \of F Y h:i:s A')."\n";

?>

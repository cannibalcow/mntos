<?php
	$xml = new DomDocument;
	$xml->load('nagios.xml');

	$xsl = new DomDocument;
	$xsl->load('nagios.xsl');

	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl); 

	$xpath = new DomXPath( $xml ); /* For grabbing info with Dom */

	if(isset($_GET['network_id']) == TRUE) {	
		$proc->setParameter('', 'network_id', $_GET['network_id']);
	} else { 
		$proc->setParameter('', 'network_id', "1");
	}

	if((isset($_GET['style']) == TRUE) && (file_exists($_GET['style'].'.css'))) {	
		$proc->setParameter('', 'style', $_GET['style']);
	} else { 
		$proc->setParameter('', 'style', "default");
	}

	if(isset($_GET['critsound']) == TRUE) {
		$critsound = $_GET['critsound'];
	} else {
		$critsound = 1;
	}
	$proc->setParameter('', 'critsound', $critsound);

	/* Critcial Code  */

	$res = $xpath->query( '//service/critical' );
	$crits = 0;
	$crit = array();
	$count = 0;
	foreach( $res as $ent ) {
		    $crits += $ent->textContent."\n";
     		    $crit[$count] = $ent->textContent;
		    ++$count;
	}

	$res = $xpath->query( '//unimportant/critacknowledged' );
	$critacks = 0;
	$critack = array();
	$count = 0;
	foreach( $res as $ent ) {
		    $critacks += $ent->textContent."\n";
     		    $critack[$count] = $ent->textContent;
		    ++$count;
	}

	$removemute = 0;

	if(isset($_GET['mute']) == TRUE) {	
		
		$proc->setParameter('', 'mute', $_GET['mute']);

		/* Code to check if mute isn't needed
		   Thanks, Svartjohan			*/

		if ($_GET['mute'] == "1") {
			$downs = 0;
			$acked = 0;

			$down = array();
			$ack = array();
			
#			$xpath = new DomXPath( $xml );

			$count = 0;
			$res = $xpath->query( '//hostheader/down' );
			foreach( $res as $ent ) {
				$downs += $ent->textContent."\n";
				$down[$count] = $ent->textContent;
				++$count;
			}

			$count = 0;
			$res = $xpath->query( '//unimportant/acknowledged' );
			foreach( $res as $ent ) {
				$acked += $ent->textContent."\n";
				$ack[$count] = $ent->textContent;
				++$count;
			}

			if (($downs > $acked) || (($crits > $critacks) && ($critsound == 1))) { $removemute = 0; }
			else { $removemute = 1; }
		}


	} else { 
		$proc->setParameter('', 'mute', "0");
	}

	$proc->setParameter('', 'removemute', $removemute);
	$proc->setParameter('', 'crits', $crits);
	$proc->setParameter('', 'critacks', $critacks);



	/* START: Code for Smartrefresh feature */

	$xpathtimestamp = new DomXPath( $xml );
	$res = $xpathtimestamp->query( '/nagios/timestamp/stamp' );
	foreach ( $res as $ent ) { $timestamp = $ent->textContent; }
	$timestamp = explode(" ", $timestamp);
	$timestamp = explode(":", $timestamp[4]);
	$timestamp = $timestamp[2];

	$nowtime = @date("s");
	$refreshdif = 2; /* Sets the time to add until calculated next Nagiosdata fetch  */
	$refreshfreq = 60; /* Set to how much time between Nagios data collecting (def: 60)  */
	
	if ($timestamp > $nowtime) {
		$refreshtime = ($timestamp - $nowtime) + $refreshdif;
	}
	elseif ($timestamp < $nowtime) {
		$refreshtime = (($timestamp + $refreshfreq) - $nowtime) + $refreshdif;
	}
	elseif ($timestamp == $nowtime) {
		$refreshtime = $refreshdif;
	}
	else {
		$refreshtime = $refreshfreq;
	}

	$proc->setParameter('', 'refreshtime', $refreshtime);

	/* END: Code for smartrefresh feature */

	$content = $proc->transformToXML($xml);

	print $content;
?>

<?php

/*
 * Configuration class. Handles configfiles.
 */

class config {

	public function getConfig($filename, $part) {
		
		if(is_file($filename) !== TRUE)
			die("No such file: ". $filename ."\n");

		$contacts = parse_ini_file($filename, $part) or die("Error when parsing file: ". $filename ."\n");
		
		return $contacts;

	}
}

/*
 * Nagios class wich deals with gathering data.
 */
		
class nagios {

	public function getStatus($config) {

		$urls = $this->getUrls($config);
		$x = 0;

		for($i = 0 ; $i < count($urls) ; $i++) {
			$status[$i] = array(	"hostheader" => array(),
						"hostun" => array(),
						"service" => array(),
						"mntoserror" => array());
		}

		foreach($urls as $key => $value) {
		print "$x";		
			$dom = new DomDocument();

			if (!@$dom->loadHTMLFile($value)) {
				array_push($status[$x]['mntoserror'], "1 error");
				unset($dom);
				$x++;
				continue;
			}

			$tags = $dom->getElementsByTagName('td');
			$ackgrab = 0;
			$critgrab = 0;

			foreach( $tags as $tag ) {

				if(strlen($tag->GetAttribute('class')) < 1) {
					next;
				} else {
					if($tag->getAttribute('class') == "hostHeader") { 
						array_push($status[$x]['hostheader'], $tag->childNodes->item(0)->nodeValue);
					}
					if($tag->getAttribute('class') == "hostUnimportantProblem") {
						$ackcheck = explode(" ", $tag->childNodes->item(0)->nodeValue);
						if (($ackcheck[1] == "Acknowledged") && ($ackgrab == 0)) { 
							array_push($status[$x]['hostun'], $tag->childNodes->item(0)->nodeValue);
							$ackgrab = 1; 
						}

					}
					# serviceImportantProblem
					if($tag->getAttribute('class') == "serviceUnimportantProblem") {
						$ackcheck = explode(" ", $tag->childNodes->item(0)->nodeValue);
						if (($ackcheck[1] == "Acknowledged") && ($critgrab == 0)) { 
							array_push($status[$x]['hostun'], $ackcheck[0] . " CritAcknowledged");
							$critgrab = 1; 
						}

					}

					if($tag->getAttribute('class') == "serviceHeader") {
						array_push($status[$x]['service'], $tag->childNodes->item(0)->nodeValue);
					}
				}
			}
			if (count($status[$x]['hostun']) == 0) {
				array_push($status[$x]['hostun'], "0 Acknowledged");	
				array_push($status[$x]['hostun'], "0 CritAcknowledged");
			}
			else {
				$ackset = 0;
				$ackcset = 0;
				foreach ($status[$x]['hostun'] as $ivar) {
					$ivar = explode(" ", $ivar);
					if ($ivar[1] == "Acknowledged") { $ackset = 1; }
					if ($ivar[1] == "CritAcknowledged") { $ackcset = 1; }
				}
				if ($ackset == 0) { array_push($status[$x]['hostun'], "0 Acknowledged");  }
				if ($ackcset == 0) { array_push($status[$x]['hostun'], "0 CritAcknowledged"); }
			}
			unset($dom);
			$x++;
		}
		return $status;
}

	private function getUrls($config) {
			
		if(is_array($config) !== TRUE) 
			die("Not an config array<br>\n");
	
		foreach($config as $key => $value) {
			
			$urls[$key] = $config[$key][nagios];
		}
		return $urls;
	}
}
/*
 * Nagios xml class. Creates xml object.
 */

class nagiosxml {
	
	public function createXML($xml, $contacts, $networks, $net_status) {

		$xmlroot = $xml->createElement('nagios');
		$xmlroot = $xml->appendChild($xmlroot);

		$xmlnetworks = $xml->createElement('networks');
		$xmlnetworks = $xmlroot->appendChild($xmlnetworks);

		$xmlcontacts = $xml->createElement('contacts');
		$xmlcontacts = $xmlroot->appendChild($xmlcontacts);

		/* timestamp xml */

		$xmltimestamp = $xml->createElement('timestamp');
		$xmltimestamp = $xmlroot->appendChild($xmltimestamp);
		
		$xmlstamp = $xml->createElement('stamp');
		$xmlstamp = $xmltimestamp->appendChild($xmlstamp);

		$xmldate = $xml->createTextNode(date("r"));	
		$xmldate = $xmlstamp->appendChild($xmldate);

	foreach($contacts as $key => $value) {
	
	/* Element struct */

		$xmlcon = $xml->createElement('contact');
		$xmlcon->setAttribute("id",$contacts[$key][id]);
		$xmlcon = $xmlcontacts->appendChild($xmlcon);
		
		$xmlname = $xml->createElement('name');
		$xmlname = $xmlcon->appendChild($xmlname);

		$xmladdress = $xml->createElement('address');
		$xmladdress = $xmlcon->appendChild($xmladdress);

		$xmlzipcode = $xml->createElement('zipcode');
		$xmlzipcode = $xmlcon->appendChild($xmlzipcode);
		
		$xmlcity = $xml->createElement('city');
		$xmlcity = $xmlcon->appendChild($xmlcity);

		$xmlcountry = $xml->createElement('country');
		$xmlcountry = $xmlcon->appendChild($xmlcountry);

		$xmlemail = $xml->createElement('email');
		$xmlemail = $xmlcon->appendChild($xmlemail);

		$xmlwrkphone = $xml->createElement('workphone');
		$xmlwrkphone = $xmlcon->appendChild($xmlwrkphone);
	
		$xmlprivatephone = $xml->createElement('privatephone');
		$xmlprivatephone = $xmlcon->appendChild($xmlprivatephone);

		$xmlprofession = $xml->createElement('profession');
		$xmlprofession = $xmlcon->appendChild($xmlprofession);

	/* Element Data */

		$text = $xml->createTextNode(utf8_encode($contacts[$key][name]));
		$text = $xmlname->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($contacts[$key][address]));
		$text = $xmladdress->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($contacts[$key][zipcode]));
		$text = $xmlzipcode->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($contacts[$key][city]));
		$text = $xmlcity->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($contacts[$key][country]));
		$text = $xmlcountry->appendChild($text);	
	
		$text = $xml->createTextNode(utf8_encode($contacts[$key][email]));
		$text = $xmlemail->appendChild($text);
		
		$text = $xml->createTextNode(utf8_encode($contacts[$key][workphone]));
		$text = $xmlwrkphone->appendChild($text);
		
		$text = $xml->createTextNode(utf8_encode($contacts[$key][privatephone]));
		$text = $xmlprivatephone->appendChild($text);
			
		$text = $xml->createTextNode(utf8_encode($contacts[$key][profession]));
		$text = $xmlprofession->appendChild($text);
	}	
	
	$i = 0;

	foreach( $networks as $key => $value ) {
	
		/* base information static data from .ini file */ 

		$xmlnet = $xml->createElement('network');
		$xmlnet->setAttribute("id", $networks[$key][id]);
		$xmlnet = $xmlnetworks->appendChild($xmlnet);

		$xmllocation = $xml->createElement('location');
		$xmllocation = $xmlnet->appendChild($xmllocation);

		$xmlnetwork = $xml->createElement('network');
		$xmlnetwork = $xmlnet->appendChild($xmlnetwork);

		$xmlpublic = $xml->createElement('public');
		$xmlpublic = $xmlnet->appendChild($xmlpublic);

		$xmlnetcon = $xml->createElement('contacts');
		$xmlnetcon = $xmlnet->appendChild($xmlnetcon);

		$xmlicon = $xml->createElement('icon');
		$xmlicon = $xmlnet->appendChild($xmlicon);

		/* fill xml with data */	
		$text = $xml->createTextNode(utf8_encode($networks[$key]['location']));
		$text = $xmllocation->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($networks[$key]['network']));
		$text = $xmlnetwork->appendChild($text);

		$text = $xml->createTextNode(utf8_encode($networks[$key]['public']));
		$text = $xmlpublic->appendChild($text);

		$con_ids = explode(",", $networks[$key][contacts]);

		for( $ids = 0; $ids < count($con_ids); $ids++) {

			$xmlcontactid = $xml->createElement('contact');
			$xmlcontactid->SetAttribute("contact_id", $con_ids[$ids]);
			$xmlcontactid = $xmlnetcon->appendChild($xmlcontactid);

			$xmlcontext = $xml->createTextNode($con_ids[$ids]);
			$xmlcontext = $xmlcontactid->appendChild($xmlcontext);
		
		}
		
		$text = $xml->createTextNode(utf8_encode($networks[$key][icon]));
		$text = $xmlicon->appendChild($text);

		if (count($net_status[$i]['mntoserror'])) {
			$xmlhost = $xml->createElement('error');
			$xmlhost = $xmlnet->appendChild($xmlhost);

			$sliced = array("1", "error");

			$xmlhostchild = $xml->createElement(strtolower($sliced[1]));
			$xmlhostchild = $xmlhost->appendChild($xmlhostchild);

			$xmlhosttext = $xml->createTextNode($sliced[0]);
			$xmlhosttext= $xmlhostchild->appendChild($xmlhosttext);

			/* Skip to next network */
			++$i;
			continue;
		}


		/* create host, service and unimportant */

		$xmlhost = $xml->createElement('hostheader');
		$xmlhost = $xmlnet->appendChild($xmlhost);

		$xmlservice = $xml->createElement('service');
		$xmlservice = $xmlnet->appendChild($xmlservice);
	
		$xmlunim = $xml->createElement('unimportant');
		$xmlunim = $xmlnet->appendChild($xmlunim);
		
		/* dynamic xml host status part */


			reset($net_status);
			foreach($net_status[$i]['hostheader'] as $value) {

				$sliced = explode(" ", $value);	
			
				$xmlhostchild = $xml->createElement(strtolower($sliced[1]));
				$xmlhostchild = $xmlhost->appendChild($xmlhostchild);

				$xmlhosttext = $xml->createTextNode($sliced[0]);
				$xmlhosttext= $xmlhostchild->appendChild($xmlhosttext);
			}
			reset($net_status);
			foreach($net_status[$i]['service'] as $value) {
				$sliced = explode(" ", $value); 
				$xmlservdata = $xml->createElement(strtolower($sliced[1]));
				$xmlservdata = $xmlservice->appendChild($xmlservdata);

				$xmlservtext = $xml->createTextNode($sliced[0]);
				$xmlservtext = $xmlservdata->appendChild($xmlservtext);
			}
			reset($net_status);

			$acknum = count($net_status[$i]['hostun']);

				if ($acknum == 0) {
					$xmlun = $xml->createElement('acknowledged');
					$xmlun = $xmlunim->appendChild($xmlun);
					$xmluntext = $xml->createTextNode('0');
					$xmluntext = $xmlun->appendChild($xmluntext);
					$xmlun = $xml->createElement('critacknowledged');
					$xmlun = $xmlunim->appendChild($xmlun);
					$xmluntext = $xml->createTextNode('0');
					$xmluntext = $xmlun->appendChild($xmluntext);

				} else {
				foreach($net_status[$i]['hostun'] as $value) {
					$sliced = explode(" ", $value);
					$xmlun = $xml->createElement(strtolower($sliced[1]));
					$xmlun = $xmlunim->appendChild($xmlun);
					$xmluntext = $xml->createTextNode($sliced[0]);
					$xmluntext = $xmlun->appendChild($xmluntext);
				}
				}
			$i++;
			}
		}
	}

/*
 * Simple time for messureing script run
 */

class Timer
{
	private $m_Start;

	   public function __construct() {
	       $this->m_Start = 0.0;
	   }

	private function GetMicrotime() {
		list($micro_seconds, $seconds) = explode(" ", microtime());
		return ((float)$micro_seconds + (float)$seconds);
	}

	public function Init() {
		$this->m_Start = $this->GetMicrotime();
	}

	public function GetTime($decimals = 2) {
		return number_format($this->GetMicrotime() - $this->m_Start, $decimals, '.', '');
	}
}

?>

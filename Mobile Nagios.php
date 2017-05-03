<?php

//----------------------------
//--Mobile Nagios (php) v1.0--
//----------------------------
//--------June 27 2017--------
//--------Aaron Porter--------
//----------------------------


//-------------------------------------------------------
// This program looks at a .dat file finds the specific 
// parts needed and encodes those parts into a json file.
//-------------------------------------------------------

//-----------------------------------------------------------------------
// Improvements to be made:
//	Looks good at the moment.
//-----------------------------------------------------------------------




//-------------
//--Variables--
//-------------

// Declaring variables and arrays to edit later
// Also opening a file
$file = (fopen("status.dat", "r"));
$plugin_array = array();
$performance_array = array();
$hoststatus = array();
$servicestatus = array();

//---------------
//--Clean Input--
//---------------

// This is a function that takes a string and checks that it is
// Only alpha-numeric
function clean($z){
    $z = strtolower($z);
    $z = preg_replace('/[^a-z0-9 -]+/', '', $z);
    $z = str_replace(' ', '-', $z);
    return trim($z, '-');
}

//----------------
//--File Reading--
//----------------


//This pulls the type, whether it be status or host from the address bar
$choice = clean($_GET['type']);
//$choice = "service";
//$choice = "host";

// Loops through file until it reaches the end
while (!feof($file)) {

	// Takes the next line
	$line =  fgets($file);
	// If it would be commented ignore it
	if ("#" == ($line[0])){ 
			      }
	
	// If a line starts with host status and then the given input from 
	// the user is host, get the name of the host
	// then loop through the lines until you find plugin_output
	// and performance_data then break if you find a curly brace

	elseif ("hoststatus" == (substr( $line, 0, 10))) {
		$plugin = " ";
		$performance = " ";
		if ($choice == "host") {
			$host =  trim(substr(fgets($file), 11));
			while (preg_match("/\s+}/",$line) != True) {
				$line = fgets($file);
				if (preg_match('/\s+plugin_output=/',$line)) {
					$plugin =  trim(substr($line,15));
				    		}
				elseif (preg_match('/\s+performance_data=/',$line)) {
					$performance = trim(substr($line, 18));
			                	  }
				else {
		             	     }	
		                  	}

			
			$hoststatus[$host]['perf'] = $performance;
			$hoststatus[$host]['plugin'] = $plugin;
				     }				 
			    				}

	// If a line starts with servicestatus and the given input from the user
	// is service, get the name of the service  then loop throught the lines
	//until you find plugin_output or the end bracket
	elseif ("servicestatus" == (substr( $line, 0, 13))) {
		if ($choice == "service") {
        		        $host =  trim(substr(fgets($file), 11));
				$description =  trim(substr(fgets($file), 21));
                 		$plugin = " ";
                		while (preg_match("/\s+}/",$line) != True) {
                        		$line = fgets($file);
                       	 		if (preg_match('/\s+plugin_output=/',$line)) {
                                		$plugin =  trim(substr($line,15));
					      		}
                               	 	else {
              		             		}
					         }
				$servicestatus[$host][$description] = $plugin;
					 }
							     }
	// If it is anything else we dont want it.
	else {
	     }
			}

//-----------------
//--JSON Encoding--
//-----------------

// Based on the input we make a json file for with the
// information we gathered.
if ($choice == "host"){
 echo json_encode($hoststatus);
}
if ($choice == "service"){	
 echo json_encode($servicestatus);
}

// Finally close the file.
fclose($file);

?>

<?php
    
    $ip=getenv('UPTIME_HOSTNAME');
	$user=getenv('UPTIME_CIM-USERNAME');	
    $password=getenv('UPTIME_CIM-PASSWORD');
	$port=getenv('UPTIME_CIM-PORT');
	$root=getenv('UPTIME_CIM-ROOT');
	$cim_class=getenv('UPTIME_CIM-CLASS');
	$cim_property=getenv('UPTIME_CIM-PROPERTY');
	$cim_action=getenv('UPTIME_CIM-ACTION');
	$cim_index_property=getenv('UPTIME_CIM-INDEX-PROPERTY');	
	$cim_single_type=getenv('UPTIME_CIM-SINGLE-TYPE');


	if(($cim_action == "Single") && ($cim_index_property != "")) {
		echo "Single data was selected.  No Index is needed. \n";
		exit(2);
	}
	if(($cim_action == "Single") && ($cim_single_type == "")) {
		echo "Single data was selected.  Please select a datatype. \n";
		exit(2);
	}
	if(($cim_action == "Multi") && ($cim_single_type != "")) {
		echo "Multi data was selected.  Only numeric data will be returned.  Please do not set the datatype. \n";
		exit(2);
	}
	if(($cim_action == "Multi") && ($cim_index_property == "")) {
		echo "Multi data was selected.  Please specify the property that should be used for the index.\n";
		exit(2);
	}
	
	
    if($user !='' && $password!='' && $ip!=''){    
	
		// Backend Controller
        $cmd_str ="https://$user:$password@$ip:$port$root:$cim_class";    
        
        $result=shell_exec("wbemcli -noverify ei '$cmd_str'");    
        $allLines = explode("\n",$result); 
		
		$found_single_property = false;
                $value = "";
                $index = "";
		
		foreach ($allLines as $currentLine){
			if (!empty($currentLine)) {
				$allProperties= explode(',',$currentLine);
				foreach ($allProperties as $currentProperty){
					$currentPropertyStrings = explode('=',$currentProperty);
					$currentPropertyValue = str_replace('"', '', $currentPropertyStrings[1]);
					
					if($cim_action == "Single") {
						if(preg_match("/$cim_property/", trim($currentProperty))){
							if ($cim_single_type == "String") {
								echo "propertyString ".$currentPropertyValue."\n";
							} elseif ($cim_single_type == "Numeric") {
								echo "propertyNumeric ".$currentPropertyValue."\n";
							}
							
							$found_single_property = true;
							break;
						}
						
					} elseif($cim_action == "Multi") {
						if(preg_match("/$cim_property/", trim($currentProperty))){
							$value = $currentPropertyValue;
						}
						if(preg_match("/$cim_index_property/", trim($currentProperty))){
							$index= $currentPropertyValue;
						}
						if(($value != "") && ($index != "")) {
					           echo $index.".multiProperty ".$value."\n";
                                                   $value = "";
                                                   $index = "";
                                                }
					}
				}
			}
			if($found_single_property && ($cim_action == "Single")) {
				break;
			}
		}
		
        
		
    }else{    
        echo "please enter the correct parameter Ex:- php xxxx.php admin admin_pass 000.000.000.000\n";
    }
?>

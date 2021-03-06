<?php

	class InfobloxInterface {

	    // property declaration
	    public $hostname;
	    public $username;
	    public $password;

	    public function __construct($host, $user, $pass){

	    	$this->hostname = $host;
	    	$this->username = $user;
	    	$this->password = $pass;



	    }



	    private function restCall($httpmethod, $username, $password, $url, $json = null, $data = null) {

	    	$ch = curl_init();   //init curl


	    	curl_setopt($ch, CURLOPT_URL, $url); //define url
	    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpmethod);

	    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  //UN and PW

			# if POST, set content-type and legnth
			if($httpmethod == "POST"){

				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
					'Content-Type: application/json',                                                                                
					'Content-Length: ' . strlen($json))                                                                       
				); 

				print "got here\n";

			}
			else if ($httpmethod == "DELETE"){



			}

	    	$output['data'] = curl_exec($ch);
			$output['headers'] = curl_getinfo($ch);


			curl_close($ch);

			return $output;


	    }

	    // method declaration
	    public function createDNSEntry($entry, $ip, $extattri=null) {

	        $ch = curl_init();   //init curl

	        $url = $this->hostname . "/wapi/v1.2/record:host";

	        if (is_null($extattri)){

	        	$json = json_encode(array(
							 "name" => "$entry",
							 "ipv4addrs" => array(array(
								"ipv4addr" => "$ip"
							 ))
						));


	        }
	        else {

	        	
	        	$json = array(
							 "name" => "$entry",
							 "ipv4addrs" => array(array(
								"ipv4addr" => "$ip"
							 ))
							 
							  
						);      

				$attri = array(
								$extattri[0][0] => array(
									"value" => $extattri[0][1]
								 )
							 );


				$json["extattrs"] = $attri;


	        	$json = json_encode($json);

	        }
	        

	        $this->restCall("POST", $this->username, $this->password, $url, $json);
			

	    }

	    /***

			Delete DNS Entry
			Supply Grid URL, username, password, and FQDN

	    ***/

	    public function deleteDNSEntry($entry) {

	    	$ch = curl_init();   //init curl

	    	$url = $this->hostname . "/wapi/v1.2/record:host?name~=" . $entry;


			curl_setopt($ch,CURLOPT_URL,$url); //define url
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers

			$data['data'] = curl_exec($ch);
			//$output['headers'] = curl_getinfo($ch);

			$ipdata = $data['data'];
			$ipdata = json_decode($ipdata,true);
			$ref = $ipdata[0]["_ref"];


			$url = $this->hostname . "/wapi/v1.2/" . $ref;

			curl_setopt($ch, CURLOPT_URL,$url); //define url
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers

			$output['data'] = curl_exec($ch);
			//$output['headers'] = curl_getinfo($ch);


			curl_close($ch);
			return $output;
	    	
	    }


		/***

			Edit DNS Entry
			Supply FQDN ofentry to edit

	    ***/
	    public function editDNSEntry($entry, $newip) {


	    	$ch = curl_init();   //init curl

	    	$url = $this->hostname . "/wapi/v1.2/record:host?name~=" . $entry;


	    	curl_setopt($ch, CURLOPT_URL,$url); //define url
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers

			$data['data'] = curl_exec($ch);
			//$output['headers'] = curl_getinfo($ch);

			$ipdata = $data['data'];
			$ipdata = json_decode($ipdata,true);
			$ref = $ipdata[0]["_ref"];


			$url = $this->hostname . "/wapi/v1.2/" . $ref;

			$json = json_encode(array(
			 "ipv4addrs" => array(array(
				"ipv4addr" => "$newip"
				 ))
			));

			
			$ch = curl_init();   //init curl

			curl_setopt($ch, CURLOPT_URL,$url); //define url
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($json))                                                                       
			);  
			$output['data'] = curl_exec($ch);
			$output['headers'] = curl_getinfo($ch);


			curl_close($ch);




	    }


	    /***

			Get DNS Entry by hostname
			Supply Grid URL, username, password, and FQDN

	    ***/

	    public function getEntryByHost($url,$username,$password,$host) {

	    	$ch = curl_init();   //init curl

			curl_setopt($ch,CURLOPT_URL,$url); //define url
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers

			$output['data'] = curl_exec($ch);
			//$output['headers'] = curl_getinfo($ch);


			curl_close($ch);
			return $output;


	    }

	    /***

			Get DNS Entry by IP
			Supply Grid URL, username, password, and FQDN

	    ***/

	    public function getEntryByIP($url,$username,$password,$host) {

	    	$ch = curl_init();   //init curl

			curl_setopt($ch,CURLOPT_URL,$url); //define url
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  //http auth type basic
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  //UN and PW
		    //curl_setopt($ch,CURLOPT_HEADER, false);  //false to not return headers

			$output['data'] = curl_exec($ch);
			//$output['headers'] = curl_getinfo($ch);


			curl_close($ch);
			return $output;


	    }

	    
	}

?>

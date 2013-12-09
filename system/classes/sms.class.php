<?php
class sms {

	public $core;

	function __construct($core){
		$this->core = $core;
	}


	public function sendSMS($recipients, $message);
		$username = $this->core->conf['sms']['username'];
		$password = $this->core->conf['sms']['password'];

		$message = urlencode($message);
		
		$count = count($recipients);
	
		$curl_arr = array();
		$master = curl_multi_init();
		
		$i=0;
		
		foreach($recipients as $current){
			$server = $this->core->conf['sms']['server'];
			$url = $server . '?username='.$username.'&password='.$password.'&from='.$username.'x&to='.$current.'&text='. $message . $i;
			$curl_arr[$i] = curl_init($url);
			curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($master, $curl_arr[$i]);
		
			$i++;
		}

		do {
		    curl_multi_exec($master,$running);
		} while($running > 0);
	
		for($i = 0; $i < $count; $i++){
		    $results[] = curl_multi_getcontent ( $curl_arr[$i]  );
		}
		
		return $results;
	}

	public function prepareInternationalNumbers($recipients){
		$i=0;

		foreach($recipients as $phone){
			$phone = ltrim($phone, '0');
			$countrycode = $this->core->conf['sms']['countrycode'];
			$recipient[$i] = $countrycode . $phone;
			$i++;
		}

		return $recipient;
	}

	public function ajaxSMSHandler($recipients, $message){

		$results = $this->sendSMS($recipients, $message);

		$i=0;
		foreach($recipients as $phone){

			$check = $results[$i];
			$success = substr_count($check, 'success');

			if($success==0){
				$output[$phone] = FALSE;		
			} else {
				$output[$phone] = TRUE;		
			}

			$i++;
		}

		$output = json_encode($output);

		return $output;
	}

}
?>

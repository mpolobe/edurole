<?php
class mailcount {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		$this->service->json = TRUE;
		
		return $this->service;
	}

	public function runService($core) {
		$this->core = $core;
	
		include $this->core->conf['conf']['classPath'] . "mail.inc.php";
		
		try{
			$mail = new mailOperations($this->core);
			$output['success'] = TRUE;
			$output['mailcount'] = $mail->mailCount();
			if($output['mailcount'] == false){
				$output['success'] = FALSE;
			}
		} catch (Exception $e) {
			$output['success'] = FALSE;
		}
		
		echo json_encode($output);
	}

}
?>

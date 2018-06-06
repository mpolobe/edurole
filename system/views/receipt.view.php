<?php

class receipt {

	public $core;
	public $view;
	public $item = NULL;


	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}
	


	public function showReceipt($item) {

		$path = "datastore/output/receipts/";
		$filename = $path. $item . ".pdf";


		$sql = "SELECT * FROM `transactions` 
			WHERE `transactions`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		$owner = $this->core->userID;
		$name = $item . "-rcpt-" .date('Y-m-d');
		include $this->core->conf['conf']['classPath'] . "security.inc.php";
		$security = new security();
		$security->buildView($this->core);
		$name = $security->qrSecurity($name, $owner, $item, $name);



		if(file_exists($filename)){
			echo'<a href="/edurole/datastore/output/receipts/'.$item.'.pdf">Download already generated receipt</a>';
			die();
		}

		while ($fetch = $run->fetch_assoc()) {
			

			$today =  date("Y-m-d");
			$admin = $this->core->userID;
			$owner = $this->core->userID;

			$output .= '<div style="position: absolute; right: -20px; font-size: 7pt; text-align: center; float:right; ">
					<img src="/data/website/edurole/datastore/output/secure/'.$name.'.png"><br>'.$name.'
			</div><center>
			<img height="100px" src="/data/website/edurole/templates/edurole/images/header.png" />
			<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">PAYMENT CONFIRMATION RECEIPT</div></div>
			<h2>OFFICIAL RECEIPT: KNU-'.$item.'</h2><hr>
			</center>';



			$output .=  '<h2>PAYMENT DETAILS '.$item.'</h2><br><table width="768" border="0" cellpadding="5" cellspacing="0">
                  	<tr class="heading">
                  	  <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                  	  <td width="200" bgcolor="#EEEEEE"></td>
                	    <td  bgcolor="#EEEEEE"></td>
               	  	 </tr>
               	   	<tr>
                    	<td><strong>Transaction ID</strong></td>
                    	<td> <b>' . $fetch["TransactionID"] . '</b></td>
                    	<td></td>
                  	</tr>
				  <tr>
                    	<td><strong>Receipted by Owner</strong></td>
                    	<td> <b>'.$admin.'</b></td>
                   	 <td></td>
                  	</tr>
                  	<tr>
                  	  <td><strong>Student</strong></td>
                   	 <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch["StudentID"] . '">' . $fetch["Name"] . '</a></td>
                  	 <td></td>
                  	</tr>
                   	<tr>
                   	 <td><strong>Student ID</strong></td>
                    	<td><b>' . $fetch["StudentID"] . '</b></td>
                    	<td></td>
                  	</tr>
                  	<tr>
                    	<td><strong>Transaction Date</strong></td>
                    	<td>' . $fetch["TransactionDate"] . '</td>
                    	<td></td>
                  	</tr>
                  	<tr>
                  	  <td><strong>Amount</strong></td>
                    	<td><b>' . $fetch["Amount"] . '</b> KWACHA</td>
                    	<td></td>
                 	 </tr>
                  	<tr>
                    	<td><strong>Name</strong></td>
                    	<td>' . $fetch["Name"] . '</td>
                    	<td></td>
                  	</tr>
                  	<tr>
                    	<td><strong>Type</strong></td>
                    	<td>' . $fetch["Type"] . '</td>
                    	<td></td>
                  	</tr>
                  	<tr>
                   	 <td><strong>Status</strong></td>
                   	 <td>' . $fetch["TS"] . ' TRANSACTION</td>
                   	 <td></td>
                  	</tr>
                 	 <tr>
                    	<td><strong>Status</strong></td>
                    	<td>' . $fetch["Error"] . ' TRANSACTION</td>
                   	 <td></td>
                 	 </tr>
			 </table>';

		}



	

		$sql = "INSERT INTO `receipts` (`ID`, `OfficerID`, `StudentID`, `DateTime`, `Hash`, `TotalAmount`, `PrintCount`) 
			VALUES (NULL, '$owner', '$item', NOW(), '', '$totalpayed', '1');";

		$this->core->database->doInsertQuery($sql);
		$receiptno = $this->core->database->id();
		$receiptno = str_pad($receiptno, 6, '0', STR_PAD_LEFT);



		require_once $this->core->conf['conf']['libPath'] . 'dompdf/dompdf_config.inc.php';



		$dompdf= new Dompdf();
		//$dompdf->setPaper('A4', 'portrait');
		$dompdf->load_html($output);
		$dompdf->render();



		//$dompdf->stream();
		$pdf = $dompdf->output();


		file_put_contents($filename, $pdf);


		if(file_exists($filename)){
			echo'<a href="/edurole/datastore/output/receipts/'.$item.'.pdf">Download already generated receipt</a>';
			die();
		}

	}
}
?>

<?php
class billing {

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

	public function editBilling($item) {
		echo'<div class="col-lg-12 greeter" style="">Billing Reversal</div>';
		echo'<p><b>To reverse this billing press the following button</b></p>';
		echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center">
		<b><a href="' . $this->core->conf['conf']['path'] . '/billing/reverse/'. $item .'">Reverse billing</a></b></div>';
		echo'<p><br></p>';
	}

	public function addBilling($item) {
		if(isset($_GET['amount'])){
			$amount = $_GET['amount'];
			$description = $_GET['description'];
			$type = $_GET['type'];

			$paymentid = "NCE-" . date("Y-m-d-H-i-s-$item");
		}
		include $this->core->conf['conf']['formPath'] . "addbilling.form.php";
	}

	public function reverseBilling($item, $unlock) {

		if($this->core->cleanGet['unlock'] == "TRUE" || $unlock == TRUE){
			$unlock = TRUE;
		}

		$sql  = "UPDATE `billing` SET   `billing`.`Description` =  'REVERSED' 
			 WHERE  `billing`.`ID` =  $item;";

		$run = $this->core->database->doInsertQuery($sql);

		$sql = "SELECT `Amount`, `StudentID` FROM `billing` WHERE `billing`.`ID` = '$item';";
		$rund = $this->core->database->doSelectQuery($sql);
		
		while ($row = $rund->fetch_row()) {
			$amount = $row[0];
			$userid = $row[1];
		}

		$sql  = "UPDATE `balances` SET  `Amount` =  `Amount`-$amount WHERE  `balances`.`StudentID` = '$userid';";
		$run = $this->core->database->doInsertQuery($sql);

		if($unlock == TRUE){
			$sql  = "UPDATE `fee-package-charge-link` SET `fee-package-charge-link`.`ChargedTerm` =  '201612' 
			WHERE  `fee-package-charge-link`.`StudentID` =  $userid;";

			$run = $this->core->database->doInsertQuery($sql);
		}
		
		echo '<div class="successpopup">Billing Reversed</div> ';
	}

	public function downloadBilling($item) {

		$sql = "SELECT * FROM `billing`
			LEFT JOIN `basic-information` ON `billing`.StudentID = `basic-information`.ID 
			LEFT JOIN `fee-package` ON `billing`.PackageName LIKE `fee-package`.Name
			WHERE `billing`.ID = '$item'
			ORDER BY `billing`.Date";

		$run = $this->core->database->doSelectQuery($sql);

		
		$path = "datastore/output/bills/";
		$filename = $path. $item . ".pdf";


		$name = $item . "-" .date('Y-m-d');
		include $this->core->conf['conf']['classPath'] . "security.inc.php";
		$security = new security();
		$security->buildView($this->core);
		$name = $security->qrSecurity($name, $owner, $item, $name);

		if(file_exists($filename)){
			//echo'<a href="/edurole/datastore/output/bills/'.$item.'.pdf">Download bill</a>';
			unlink($filename);
			//die();
		}

		include $this->core->conf['conf']['viewPath'] . "fees.view.php";
		$fees = new fees();
		$fees->buildView($this->core);
	

		while ($fetch = $run->fetch_assoc()) {

			$today =  date("Y-m-d");
			$admin = $this->core->userID;

			$output .= '<div style="position: absolute; right: -20px; font-size: 7pt; text-align: center; float:right; ">
					<img src="/data/website/edurole/datastore/output/secure/'.$name.'.png"><br>'.$name.'
			</div><center>
			<img height="100px" src="/data/website/edurole/templates/edurole/images/header.png" />
			<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">OFFICIAL INVOICE</div></div>
			<h2>INVOICE: KNU-'.$item.'</h2><hr>
			</center>';

			$output .=  '<h2>INVOICE DETAILS KNU-'.$item.'</h2><br>';

			$output.= '<table width="700px"  border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td  colspan="3" height="28" bgcolor="#EEEEEE"><strong>Student details</strong></td>
					  </tr>
					  <tr>
						<td width="200px"><strong>Student Number</strong></td>
						<td>' . $fetch['StudentID']. '</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Date billed</strong></td>
						<td>' . $fetch['Date'] . ' </td>
					  </tr>
					</table>';



			$output .=  $fees->showFees($fetch["PackageName"], TRUE);


			require_once $this->core->conf['conf']['libPath'] . 'dompdf/dompdf_config.inc.php';



			$dompdf= new Dompdf();
			//$dompdf->setPaper('A4', 'portrait');
			$dompdf->load_html($output);
			$dompdf->render();
			//$dompdf->stream();
			$pdf = $dompdf->output();
			file_put_contents($filename, $pdf);
	
	
			if(file_exists($filename)){
				echo'<a href="/edurole/datastore/output/bills/'.$item.'.pdf"><b>Download generated receipt</b></a>';
				die();
			}


		}

	}

	public function personalBilling($item) {
		$uid = $this->core->userID;

		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/billing/add/'.$item.'?amount=0&type=15&description=Bill">Bill student</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}


		$studentyear = substr($item, 0, 4);

		$sql = "SELECT * FROM `billing`
			LEFT JOIN `basic-information`
			ON `billing`.StudentID = `basic-information`.ID 
			WHERE `basic-information`.ID = '$item'
			ORDER BY Date";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Billed Amounts </h2><br>
		<table width="100%" height="" border="0" cellpadding="4" cellspacing="0">'.
		'<tr class="heading">' .
			'<td width="140px"><b>Bill Number</b></td>' .
			'<td width="140px"><b>Time</b></td>' .
			'<td><b>Amount</b></td>' .
			'<td width="400px"><b>Description</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_row()) {
			$reverse = FALSE;

			if($fetch[4] == "REVERSED"){
				continue;
			}


			$bid = $fetch[0];
			$uid =  $fetch[1];
			$amount =  $fetch[2];
			$date =  $fetch[3];
			$description =  $fetch[4];

			$package =  $fetch[5];

			if(!is_numeric($package)){
				$link = '<a href="' . $this->core->conf['conf']['path'] . '/billing/download/'.$bid.'">KNU-' . $bid . '</a>';
			} else {
				$link = 'KNU-' . $bid;
			}

			echo '<tr ' . $color . '>
				<td><b>'.$link.'</b></td>
				<td>' . $date . '</td>
				<td><b>' . $amount . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td>' . $description . ' </td>
				</tr>';

			$total = $amount + $total;
		}


		echo '<tr class="heading"><td><b>Total billed</b></td>' .
		'<td width="60px"></td>' .
		'<td width="165px"><b>'. $total .'  '.$this->core->conf['conf']['currency'].'</b></td>' .
		'<td width="60px"></td>' .


		'</tr>';

		echo '</table>';

	}


	public function showBilling($item) {

		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/billing/add/'.$item.'?amount=0&type=15&description=Bill">Bill student</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}


		$studentyear = substr($item, 0, 4);

		$sql = "SELECT * FROM `billing`
			LEFT JOIN `basic-information`
			ON `billing`.StudentID = `basic-information`.ID 
			WHERE `basic-information`.ID = '$item'
			ORDER BY Date";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Billed Amounts </h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width="140px"><b>Transaction ID</b></td>' .
		'<td width="120px"><b>Time</b></td>' .
		'<td width="80px"><b>Amount</b></td>' .
		'<td width="80px"><b>Student ID</b></td>' .
		'<td width=""><b>Description</b></td>' .
		'<td width="140px"><b>Management</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_row()) {
			$reverse = FALSE;


			if($fetch[14] == "AUTOMATIC"){
				$color = 'style="color: #00000;"';
			}

			if($fetch[14] == "MANUAL"){
				$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
			}

			$bid = $fetch[0];
			$uid =  $fetch[1];
			$amount =  $fetch[2];
			$date =  $fetch[3];
			$description =  $fetch[4];

			echo '<tr ' . $color . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/billing/download/'.$bid.'">KNU-' . $bid . '</a></b></td>
				<td>' . $date . '</td>
				<td><b>' . $amount . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td>' . $uid. '</td>
				<td>' . $description . ' </td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/billing/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit </a>'; 
	
				if(substr($description,0,7) != SETTLED){
					echo'<a href="' . $this->core->conf['conf']['path'] . '/billing/settle/' . $fetch[0] . '/' . $uid. '"> <img src="' . $this->core->fullTemplatePath . '/images/list.gif"> settle</a></td>';
				}
			echo'</tr>';

			$total = $amount + $total;
		}


		echo '<tr class="heading"><td><b>Total billed</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="4"><b>'. $total .'  '.$this->core->conf['conf']['currency'].'</b></td>' .

		'</tr>';

		echo '</table>';

	}


	public function settleBilling($item){
		$sub = $this->core->subitem;

		echo'<div class="col-lg-12 greeter" style="">Bill Settlement</div>';
		echo'<p><b>To settle this billing by cash press the following button</b></p>';
		echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/housing/'. $item .'/'.$sub.'">Settle accommodation bill collect CASH</a></div><div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/housing/'. $item .'/'.$sub.'?collect=FALSE">Settle accommodation bill that is already paid</a></b></div>
		<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/regular/'. $item .'/'.$sub.'">Settle regular bill</a></b></div>';
		echo'<p><br></p>';

	}


	public function regularBilling($item){
		$uid = $this->core->subitem;
		$sql = "SELECT * FROM `transactions` WHERE `transactions`.StudentID = '$uid' AND `Status` != 'REVERSED'";
		$run = $this->core->database->doSelectQuery($sql);

		echo'<form id="settlement" name="settlement" method="post" action="'.$this->core->conf['conf']['path']. '/billing/listsettle/'.$item.'/'.$uid.'">
		<div class="heading">SELECT PAYMENT(S) FOR THIS BILL TO BE SETTLED WITH</div><fieldset>';

		while ($fetch = $run->fetch_assoc()) {
			$amount = $fetch["Amount"];
			$ID = $fetch["ID"];
			$reference = $fetch["TransactionID"];
			$date = $fetch["TransactionDate"];

			echo'  <input type="checkbox" name="pay[]" value="'.$ID.'"> '.$date.' - '.$reference.' - <b>'.$amount.'</b><br>';
			$set = TRUE;
		}

		if($set != TRUE){
			echo'<div class="warningpopup">No payments for this students yet. Please add payment first!</div>';


			echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center; width: 95%;">
			<b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/'. $uid .'">BACK TO PAYMENT OVERVIEW</a></b></div>';
			echo'<p><br></p>';
		} else {
			echo'<hr></fieldset> <input type="submit" value="Submit"></form>';
		}

	}



	public function listsettleBilling($item){

		$bill = $item;
		$uid =  $this->core->subitem;
	
		$payments = $this->core->cleanPost['pay'];

		
		$amount = 0;
		foreach($payments as $item){
			
			$sql = "SELECT * FROM `transactions` WHERE `transactions`.ID = '$item' AND `Phone` != '100'";
			$run = $this->core->database->doSelectQuery($sql);
		

			while ($fetch = $run->fetch_assoc()) {
				$pay = $fetch["Amount"];
				$amount = $amount+$pay;
			

				$sql = "UPDATE `transactions` SET `Phone` = '100' WHERE `transactions`.ID = '$item'";
				$this->core->database->doInsertQuery($sql);
			}
		}

		$sql = "SELECT * FROM `billing` WHERE `billing`.ID = '$bill' LIMIT 1";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$description = "Payment for " . $fetch['Description'];


			$sql = "UPDATE `billing` SET `Description` = CONCAT('SETTLED- ', Description) WHERE `billing`.ID = '$bill';";
			$this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `balances` SET `Amount` = Amount-$amount, `LastUpdate` = NOW(), `LastTransaction` = '$description' WHERE `StudentID` = '$uid';";
			$this->core->database->doInsertQuery($sql);

			echo'<div class="successpopup">BALANCE UPDATED '.$amount.' DEDUCTED - PLEASE PRINT NEW CONFIRMATIONS STATEMENT</div>';

			echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center; width: 100%;">
			<b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/'. $uid .'">BACK TO PAYMENT OVERVIEW</a></b></div>';
			echo'<p><br></p>';
		}

	}

	public function housingBilling($item){
		$bill = $item;
		$uid = $this->core->subitem;
		$date = date('Y-m-d');
		echo'<div class="col-lg-12 greeter" style="">Accommodation bill being settled</div>';

		// COLLECTING PAYMENT

		$sql = "SELECT * FROM `billing` WHERE `billing`.ID = '$item' LIMIT 1";
		$run = $this->core->database->doSelectQuery($sql);

	

		while ($fetch = $run->fetch_assoc()) {
			$description = "Payment for " . $fetch['Description'];
			$type = '1';
			$reference = "HOUSING-" . date("Y-m-$item");
			$amount = $fetch['Amount'];

			if($this->core->cleanGet['collect'] != "FALSE"){
				include $this->core->conf['conf']['viewPath'] . "payments.view.php";
				$pay = new payments();
				$pay->buildView($this->core);
		
				$pay->makePayments($item, $uid, $amount, $description, $type, $date, $reference);
				
				echo'<div class="warningpopup">REMEMBER TO COLLECT THE PAYMENT OF '.$amount.' KWACHA</div>';
			}else{
				echo'<div class="warningpopup">PAYMENT ALREADY COLLECTED, ONLY FREEING ROOM</div>';
			}

			$sql = "UPDATE `billing` SET `Description` = CONCAT('SETTLED- ', Description) WHERE `billing`.ID = '$bill';";
			$this->core->database->doInsertQuery($sql);

		}

		// ASSIGNING ROOM
		include $this->core->conf['conf']['viewPath'] . "accommodation.view.php";
		$housing = new accommodation();
		$housing->buildView($this->core);
		$housing->approveHousing($uid);
		
	}

	public function allBilling($item){


//`basic-information`.`Status` IN ('Approved','Requesting')


		$sql = "SELECT DISTINCT `basic-information`.ID, `basic-information`.MobilePhone
			FROM `basic-information`
			WHERE `basic-information`.ID IN (20120001,
20131055,
20120002,
20120002,
20100685,
20130005,
20131056,
20130006,
20130008,
20131057,
20130010,
20131058,
20130011,
20131059,
20130013,
20130012,
20131060,
20100723,
20110023,
20110023,
20131061,
20130015,
20130016,
20130018,
20130021,
20130022,
20130024,
20131062,
20130029,
20131064,
20111643,
20111643,
20110047,
20130031,
20110051,
20130032,
20130034,
20130035,
20131065,
20130043,
20131066,
20130044,
20131067,
20130046,
20131068,
20131069,
20131070,
20131071,
20131072,
20130051,
20131073,
20131074,
20130052,
20131076,
20131077,
20131078,
20130054,
20131079,
20130056,
20111920,
20110105,
20120044,
20130058,
20131081,
20120045,
20131082,
20110111,
20130061,
20130066,
20130067,
20131084,
20131085,
20131086,
20131088,
20131116,
20131089,
20131090,
20130072,
20131091,
20130074,
20131092,
20130075,
20130076,
20130078,
20120061,
20120061,
20120061,
20131094,
20131095,
20130081,
20131096,
20130083,
20130084,
20130085,
20131097,
20130086,
20130087,
20131098,
20131099,
20130088,
20130089,
20130090,
20130091,
20130092,
20131101,
20130095,
20130097,
20120069,
20130098,
20131103,
20130099,
20131104,
20130102,
20130104,
20130106,
20131106,
20130108,
20130109,
20131108,
20130110,
20130112,
20130113,
20130114,
20110185,
20131109,
20131110,
20131111,
20130117,
20130118,
20111945,
20130120,
20131113,
20130121,
20130122,
20131114,
20130123,
20130124,
20130125,
20130126,
20110214,
20131115,
20130129,
20131117,
20131118,
20130132,
20130133,
20131119,
20130137,
20131120,
20130142,
20130143,
20131121,
20131122,
20130146,
20130147,
20130151,
20131124,
20130154,
20131126,
20131127,
20130157,
20130158,
20130159,
20130160,
20130161,
20131129,
20131131,
20130164,
20120110,
20130165,
20131132,
20131133,
20131134,
20120112,
20130167,
20130168,
20131136,
20131137,
20130171,
20130172,
20131138,
20131140,
20131141,
20130174,
20130176,
20130177,
20131142,
20131143,
20130178,
20101119,
20101119,
20130180,
20131144,
20130181,
20131145,
20130184,
20131146,
20131147,
20131148,
20130187,
20131149,
20130188,
20131150,
20131151,
20130194,
20131153,
20130198,
20130200,
20130203,
20130206,
20130209,
20131157,
20130211,
20131158,
20131160,
20131161,
20130215,
20131162,
20130216,
20130218,
20130221,
20130222,
20131164,
20130224,
20130225,
20130226,
20130229,
20131166,
20130230,
20131167,
20130233,
20131168,
20111998,
20130238,
20130239,
20130241,
20131169,
20130243,
20130244,
20131170,
20131171,
20111999,
20111999,
20130246,
20131172,
20130248,
20130250,
20130252,
20103635,
20131174,
20130258,
20130259,
20131175,
20131176,
20131177,
20131178,
20112005,
20131179,
20131181,
20131182,
20130264,
20130265,
20131183,
20131184,
20130267,
20130268,
20130269,
20131185,
20131186,
20131187,
20131188,
20130271,
20130273,
20131190,
20130276,
20101379,
20131192,
20131193,
20130280,
20130281,
20130282,
20130283,
20130285,
20130287,
20131196,
20130289,
20130290,
20120205,
20131197,
20130291,
20130294,
20130292,
20130295,
20131200,
20130296,
20131202,
20130298,
20110502,
20131203,
20131204,
20130302,
20110514,
20110514,
20110515,
20131205,
20131206,
20130378,
20131208,
20131209,
20131210,
20131212,
20131213,
20101481,
20130309,
20130310,
20130313,
20131217,
20131221,
20130314,
20131222,
20130315,
20101510,
20101510,
20131224,
20120544,
20130316,
20130317,
20130318,
20131225,
20130319,
20130321,
20130323,
20130324,
20131226,
20131012,
20131227,
20130326,
20131229,
20131230,
20130331,
20130332,
20130334,
20131231,
20112047,
20112047,
20110588,
20110588,
20131232,
20130337,
20130339,
20130341,
20130342,
20130343,
20131233,
20131234,
20130344,
20131235,
20130346,
20131237,
20131238,
20131239,
20130349,
20130350,
20131241,
20130351,
20131242,
20130353,
20130355,
20131244,
20131245,
20131246,
20130359,
20131247,
20130360,
20130361,
20131249,
20130365,
20131250,
20131251,
20130367,
20130368,
20130369,
20131252,
20110657,
20130377,
20131255,
20130379,
20130380,
20130382,
20130383,
20130384,
20120263,
20130386,
20130388,
20130389,
20130390,
20131259,
20130392,
20131260,
20131262,
20131263,
20130393,
20130394,
20131265,
20130395,
20131266,
20131267,
20131268,
20130396,
20131269,
20130397,
20130398,
20131270,
20101756,
20101756,
20130399,
20130400,
20131271,
20131272,
20131273,
20130401,
20131274,
20130403,
20131275,
20131277,
20131278,
20131279,
20131280,
20130407,
20130408,
20130409,
20130410,
20130411,
20130412,
20130413,
20101816,
20131281,
20130415,
20101829,
20101829,
20130416,
20131284,
20130419,
20131286,
20131287,
20130420,
20130421,
20131288,
20131289,
20131291,
20130423,
20130424,
20131292,
20130425,
20130426,
20131293,
20103739,
20130429,
20131295,
20110761,
20110761,
20131296,
20110763,
20131297,
20130431,
20131298,
20131299,
20131300,
20130435,
20131301,
20130444,
20101916,
20130445,
20131302,
20131303,
20130446,
20131304,
20131305,
20131307,
20131308,
20130449,
20131309,
20131310,
20130450,
20120315,
20131311,
20130451,
20131313,
20131314,
20131315,
20130452,
20131316,
20130453,
20131317,
20130454,
20130455,
20130456,
20130457,
20130458,
20130459,
20131318,
20131319,
20131321,
20130460,
20130461,
20131322,
20131323,
20130462,
20130463,
20130465,
20131325,
20131326,
20131327,
20130469,
20131329,
20130473,
20130474,
20131331,
20130475,
20102014,
20131332,
20130478,
20131333,
20131334,
20131335,
20131337,
20131338,
20131339,
20130482,
20131341,
20130484,
20131342,
20131343,
20110876,
20130485,
20130486,
20131344,
20131345,
20131347,
20131349,
20130490,
20131350,
20130493,
20131351,
20102084,
20130494,
20130496,
20130497,
20130498,
20131353,
20130499,
20110897,
20110897,
20131354,
20131355,
20131357,
20130500,
20131359,
20131360,
20131361,
20130501,
20130503,
20130504,
20130505,
20131363,
20130506,
20112130,
20131364,
20130507,
20131365,
20131367,
20102117,
20130508,
20131368,
20110923,
20130510,
20130511,
20130513,
20131370,
20130514,
20131371,
20130517,
20130519,
20131373,
20131374,
20130521,
20131375,
20130523,
20130524,
20130525,
20131376,
20130530,
20130531,
20130532,
20102162,
20130538,
20130533,
20131379,
20110968,
20130539,
20131380,
20131381,
20131382,
20130541,
20130542,
20130543,
20131383,
20130544,
20130545,
20131384,
20110792,
20131385,
20130549,
20131387,
20130551,
20130554,
20110994,
20131388,
20130559,
20130561,
20130562,
20130563,
20130564,
20131391,
20130565,
20130567,
20130569,
20130570,
20130571,
20130572,
20130573,
20130574,
20130575,
20131394,
20103818,
20130577,
20131395,
20130578,
20130579,
20130580,
20130580,
20131396,
20131397,
20131398,
20130583,
20131399,
20131400,
20131401,
20130586,
20131402,
20130587,
20130588,
20130589,
20131403,
20130590,
20130600,
20131404,
20131405,
20131406,
20130591,
20130593,
20131407,
20120437,
20130594,
20130595,
20130596,
20130535,
20130598,
20131414,
20131415,
20130601,
20130603,
20130604,
20131418,
20131419,
20130610,
20131420,
20130611,
20130614,
20131423,
20131424,
20131425,
20130615,
20131427,
20131428,
20111108,
20130617,
20131429,
20130619,
20130620,
20111116,
20111116,
20130621,
20130622,
20130623,
20130624,
20131432,
20131433,
20131434,
20130626,
20130627,
20130628,
20131436,
20131437,
20131438,
20131439,
20131440,
20130630,
20130631,
20110962,
20130632,
20131441,
20130636,
20130637,
20130638,
20102452,
20130639,
20130641,
20131443,
20130643,
20130644,
20130645,
20131444,
20130648,
20131446,
20131447,
20130652,
20130654,
20130655,
20130656,
20130657,
20111174,
20111174,
20130659,
20130660,
20131450,
20131451,
20131452,
20130666,
20131453,
20130667,
20130669,
20130671,
20130672,
20130674,
20131454,
20130677,
20130679,
20131458,
20130678,
20130352,
20130680,
20130681,
20131459,
20130682,
20130684,
20130685,
20102537,
20131461,
20131462,
20131463,
20130689,
20130691,
20130692,
20130693,
20102546,
20131464,
20130694,
20131465,
20130695,
20130696,
20102565,
20130700,
20131466,
20130701,
20130702,
20131469,
20102577,
20130705,
20131470,
20131471,
20130706,
20111260,
20131472,
20130708,
20130709,
20130710,
20131473,
20131475,
20131476,
20131477,
20131478,
20131479,
20131480,
20130713,
20130715,
20131482,
20131483,
20102627,
20130717,
20130718,
20130720,
20131484,
20131485,
20130721,
20131486,
20131487,
20120543,
20130723,
20130724,
20130725,
20131488,
20130726,
20103519,
20130729,
20131490,
20131491,
20131492,
20130733,
20130734,
20130735,
20131494,
20130736,
20130737,
20130738,
20131495,
20111334,
20131496,
20131499,
20130741,
20131500,
20131501,
20130742,
20130743,
20131503,
20130744,
20131504,
20130746,
20131505,
20131506,
20130748,
20131507,
20130753,
20130754,
20131509,
20111366,
20111371,
20131510,
20131511,
20130757,
20130758,
20111384,
20131512,
20130763,
20130747,
20130765,
20131514,
20111407,
20111407,
20130767,
20131516,
20130769,
20131517,
20131519,
20131520,
20131521,
20131522,
20131523,
20131524,
20130772,
20130773,
20131525,
20130774,
20130776,
20131528,
20131529,
20130777,
20130778,
20131531,
20130779,
20131532,
20131533,
20131534,
20131535,
20130782,
20130783,
20131536,
20130784,
20131537,
20130785,
20130786,
20131538,
20130789,
20131539,
20130791,
20131540,
20130792,
20130793,
20130794,
20130795,
20131543,
20131544,
20131545,
20130797,
20111478,
20111478,
20131547,
20131548,
20131550,
20131551,
20131552,
20130802,
20130804,
20130805,
20131554,
20120613,
20130810,
20130811,
20130813,
20130815,
20131556,
20131557,
20130816,
20130817,
20130818,
20131558,
20130819,
20131559,
20131560,
20131561,
20130822,
20130824,
20130828,
20130829,
20130830,
20130831,
20131563,
20130832,
20130833,
20130834,
20130835,
20131565,
20130836,
20130837,
20120631,
20131566,
20130838,
20102954,
20130841,
20130843,
20131567,
20130846,
20130848,
20131568,
20130850,
20131569,
20130404,
20131570,
20131572,
20130853,
20130854,
20111585,
20111585,
20130855,
20130856,
20130857,
20130859,
20130860,
20130861,
20130862,
20130864,
20130865,
20131574,
20131576,
20130868,
20131577,
20130869,
20131578,
20130870,
20131580,
20131581,
20131582,
20131583,
20120651,
20130874,
20130875,
20131584,
20130877,
20130878,
20130879,
20130880,
20130882,
20130883,
20130885,
20131586,
20131587,
20131588,
20130884,
20131589,
20130886,
20130888,
20130889,
20111633,
20131590,
20130890,
20131591,
20130894,
20131592,
20130895,
20131593,
20130899,
20131595,
20131597,
20130903,
20130904,
20130905,
20111664,
20131599,
20131600,
20130906,
20130907,
20131601,
20130909,
20130910,
20131604,
20131605,
20130911,
20131606,
20131607,
20130914,
20130927,
20130917,
20130920,
20130922,
20130923,
20131612,
20130929,
20130931,
20130932,
20130934,
20130936,
20131614,
20130937,
20130938,
20130939,
20131616,
20130940,
20131617,
20131619,
20131620,
20130942,
20130943,
20130944,
20130946,
20131624,
20130950,
20131625,
20130951,
20130952,
20131626,
20130953,
20130954,
20131627,
20131628,
20130956,
20130957,
20130958,
20130959,
20130960,
20130963,
20130965,
20130966,
20130967,
20130968,
20111752,
20111754,
20130970,
20130974,
20131634,
20130976,
20131637,
20130978,
20131639,
20130979,
20130981,
20131641,
20130982,
20130983,
20130985,
20130986,
20130987,
20130988,
20131644,
20130989,
20112332,
20112334,
20131645,
20130990,
20130991,
20131646,
20130993,
20130994,
20130995,
20131647,
20130997,
20130998,
20131649,
20131650,
20131000,
20131002,
20131653,
20131654,
20131003,
20131004,
20131005,
20131655,
20131010,
20131656,
20131011,
20120737,
20131013,
20131014,
20131660,
20131016,
20131017,
20131018,
20131661,
20131662,
20131663,
20131664,
20131020,
20131665,
20131666,
20131051,
20120751,
20131669,
20131670,
20131025,
20131027,
20131671,
20131028,
20131672,
20131029,
20131674,
20131676,
20131677,
20131678,
20131679,
20131681,
20103431,
20103431,
20131682,
20131683,
20131684,
20131685,
20120770,
20131038,
20131687,
20131688,
20131690,
20131041,
20131044,
20131691,
20131045,
20131046,
20131047,
20131692,
20131048,
20131050,
20131693)";
 
		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table table-bordered table-striped table-hover" width="100%">
			<thead>
				<tr>
					<td>Student</td>
					<td>Year</td>
					<td>Balance</td>
					<td>Category</td>
					<td>Remark</td>
				</tr>				
			</thead>';

		$i=1;
		while ($fetch = $run->fetch_row()) {
			$studentid = $fetch[0]; 
			$phone  = $fetch[1]; 
			$totalb = $this->logBilling($studentid, $phone, $i);
			$total = $total + $totalb;
			$i++;
		}

		echo'</table>';


	} 


	private function logBilling($item, $phone, $a) {
		$year = date("Y");
		$studentyear = substr($item, 0, 4);

		$currentyear = $year-$studentyear;
		// IF FIRST YEAR ADD 1 
		$currentyear++;

		if($currentyear > 4) { $currentyear = 4; }

		$registered = 'NO';
		$sqx = "SELECT COUNT(ID)  FROM `course-electives` WHERE `StudentID` = '$item' AND `Approved` = '1'";
		$runx = $this->core->database->doSelectQuery($sqx);
		while ($fetch = $runx->fetch_row()) {
			$registered = '';
		}

	
		$boardstatus = "D";
		$boarding = '';
		$sql = "SELECT * FROM `housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID";

		$run = $this->core->database->doSelectQuery($sql);
		while ($fetch = $run->fetch_row()) {
			$boardstatus = "B";
			$boarding = 'BOARDER';
		}


		$sql = "SELECT DISTINCT SUM(Amount), `fee-package`.Name 
			FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$item'
			WHERE `fee-package`.`Name` LIKE concat( ChargeType, '-$boardstatus-%-$currentyear')
			OR `fee-package`.`Name` LIKE 'GRZ-$boardstatus-%-$currentyear'
			ORDER BY `fee-package-charge-link`.ID DESC
			LIMIT 1";


		/* $sql = "SELECT DISTINCT SUM(Amount), `fee-package`.Name 
			FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$item'
			WHERE `fee-package`.`Name` LIKE concat( 'DES', '-$currentyear-%')
			ORDER BY `fee-package-charge-link`.ID DESC
			LIMIT 1";


			
			$sql = "SELECT * FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `student-data-other` ON `student-data-other`.StudentID = '$item'
			WHERE `fee-package`.`Name` = concat('DES-', YearOfStudy)
			GROUP BY `fees`.ID";
		 */

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		$total = 0;
		$totalx = 0;
		$set = FALSE;

		while ($fetch = $run->fetch_row()) {
			
			if ($fetch[1] != $previous) {

				$method = $fetch[4];
				if($fetch[0] != $previous & $i != 0){
					$totalx = $total;
				}

				$packagename = $fetch[1];
				
				$description = $fetch[2];

				$i++;

			}

			$fee = $fetch[0];
			$total =  $total + $fee;
			$previous = $fetch[1];
			$set = TRUE;
			$yeard = $fetch[1];

		}

		

		if($set == TRUE && $totalx == 0){
			$totalx = $total;
		}

		$totaldouble = $total-$totalx;

		$total = $totalx;


		// UPDATE THE BALANCE WITH LATEST FEE PACKAGE
		$sql = "SELECT * FROM `fee-package-charge-link` WHERE `StudentID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		$billed = NULL;
		while ($fetch = $run->fetch_row()) {
			$billed = $fetch[3];
		}


	

		if($total != 0){

			if($billed != '2017921'){

				//if($registered != "NO"){

					$description = 'Billing Tuition December 2017 Residential';

					$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`, `PackageName`) 
						VALUES (NULL, '$item', '$total', NOW(), '$description', '$packagename');";
					//$this->core->database->doInsertQuery($sql);


					$sql = "INSERT INTO `fee-package-charge-link`
						(ID, StudentID, ChargeType, ChargedTerm) VALUES (NULL, $item, '$yeard', '$packagename')
						ON DUPLICATE KEY UPDATE `ChargedTerm`='$packagename'";
					//$this->core->database->doInsertQuery($sql);

				//}

			
				require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
				$payments = new payments();
				$payments->buildView($this->core);
				$balance = $payments->getBalance($item);



				$nc = $total-$total-$total;
				if($balance == 0){
					$remark = "NO BALANCE / GRADUATION PAYMENT UNKNOWN";
				}else if($balance > 0){
					$remark = "STUDENT IS OWING";
				}else if($balance == -1000 || $balance == -1100){
					$remark = "GRADUATION READY WITH GOWN";
				}else if($balance == -250 || $balance == -350){
					$remark = "GRADUATION READY";
				}else if(!isset($balance)){
					$remark = "NO BALANCE KNOWN";
				} else {
					$remark = "PLEASE CHECK ACCOUNT";
				}


				/* } elseif ($balance == $nc){
					$remark = "Balance is 0 after billing";
					$remark = "PLEASE CHECK ACCOUNT"; */


  
				echo '<tr>
					<td><a href="https://www.nkrumah.edu.zm/edurole/information/show/' . $item . '">' . $item . '</td>
					<td>Year ' . $currentyear .' </td>
					<td><b>'. $balance .'</b></td>
					<td>GRADUATING STUDENT</td>
					<td>'.$remark.'</td>
				</tr>';

				$totalb = $totalb+$balance;

				echo $output;
			}
		}
		return $totalb;
	}
}
?>
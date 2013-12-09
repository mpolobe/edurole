<?php
class payments {

	public $core;
	public $view;

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
		
		if ($this->core->action == "overview" && $this->core->role > 105) {
			$this->listTransactions();
		} elseif ($this->core->action == "approve" && $this->core->role > 105) {
			$this->approveTransaction($this->core->item);
		} elseif ($this->core->action == "reject" && isset($this->core->item) && $this->core->role > 105) {
			$this->rejectTransaction($this->core->item);
		} elseif ($this->core->action == "view" && $this->core->role <= 10) {
			$this->showPayments($this->core->userID);
		}
	}

	function listTransactions($item) {
		$function = __FUNCTION__;
		$title = 'Overview of payments';
		$description = 'Overview student payments sorted by date/time';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `transactions` 
			LEFT JOIN `basic-information`
			ON `transactions`.UID = `basic-information`.ID 
			ORDER BY `transactions`.Timestamp";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/courses/add">Show list of unknown payments</a></div>'.
		'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader"><td><b>Transaction ID</b></td>' .
		'<td><b>Time</b></td>' .
		'<td><b>Transaction Amount</b></td>' .
		'<td><b>Student ID</b></td>' .
		'<td><b>Sucesfuly linked</b></td>' .
		'<td><b>Management tools</b></td>' .
		'</tr>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {
			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo '<tr ' . $bgc . '>
                    <td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/view/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>
                    <td>' . $fetch[6] . '</td>
                    <td><b>' . $fetch[7] . ' ZMW</b></td>
                    <td>' . $fetch[4] . '</td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $fetch[1] . '">' . $fetch[16] . ' ' . $fetch[18] . '</a></td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/courses/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . '/courses/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>';
	}

	function showCourse($item) {
		$function = __FUNCTION__;
		$title = 'View course information';
		$description = 'Overview of all courses currently on offer';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `courses`, `basic-information` WHERE `courses`.ID = $item AND `courses`.CourseCoordinator = `basic-information`.ID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {

			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>Course name</strong></td>
                    <td> <b>' . $fetch[1] . '</b> - ' . $fetch[3] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course coordinator</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $fetch[2] . '">' . $fetch[4] . ' ' . $fetch[6] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course enrollment</strong></td>
                    <td>COUNT</td>
                    <td></td>
                  </tr>';

		}

		echo '</table>';
	}
}

?>

<?php
class calendar {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('fullcalendar');
		$this->view->css = array('fullcalendar', 'fullcalendar.print');

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function showCalendar() {
		$inlinejs = "<script>
		$(document).ready(function() {

		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();

		var calendar = $('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
				var title = prompt('Event Title:');
				if (title) {
					calendar.fullCalendar('renderEvent',
						{
							title: title,
							start: start,
							end: end,
							allDay: allDay
						}

					);
				}
				calendar.fullCalendar('unselect');
			},
			defaultView: 'agendaWeek',
			editable: true,
			events: [ \n";


		$i = 1;
		$sql = "SELECT * FROM `calendar`";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$time = date('Y, m, d, h, i', $row[2]);
			$uid = $i++;

			$inlinejs .= '		{
			id: ' . $uid . ',
			title: \'' . $row[4] . '\',
			start: new Date(' . $time . '),
			allDay: false
			}';

			$inlinejs .= ",\n";
		}

		$inlinejs .= "		]
		});

	});

	</script>";

		echo $inlinejs;

		echo '<div id="calendar"></div>';

	}
}

?>

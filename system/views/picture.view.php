<?php
class picture{

	public $core;
	public $view;

	public function configView() {
		$this->view->header = FALSE;
		$this->view->footer = FALSE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}


	public function savePicture($item){
		$data = base64_decode($_POST['data']);

		if($this->core->role < 100){
			$item = $this->core->userID;
		}
	
		$url = '/data/website/datastore/identities/pictures/'. $item .'.png';

		file_put_contents($url, $data);
	}

	public function makePicture($item) {

		$url = $this->core->conf['conf']['path'] . '/picture/save/' . $item;

		echo'<script  type="text/javascript" src="'.$this->core->conf['conf']['path'].'/lib/jquery/jquery.js"></script>
			<style type="text/css">
				.container {
					width: 320px;
					height: 240px;
					position: relative;
					border: 1px solid #d3d3d3;
					float: left;
				}
		 
				.container video {
					width: 100%;
					height: 100%;
					position: absolute;
				}
		 
				.container .photoArea {
					border: 2px dashed white;
					width: 140px;
					height: 190px;
					position: relative;
				}
		 
				#canvas {
					margin-left: 20px;
				}
		 
				.controls {
					clear: both;
				}
			</style>

		<div style=" width: 580px; height: 50px; padding: 20px; ">

			<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to profile </a>
			<button id="startbutton" onlick="takepicture()" style="color: #FFF;  font-weight: bold; border: 0px none; padding: 5px; width: 180px; height: 40px;  font-size: 15px; background-color: #6297C3;">  <span class="glyphicon glyphicon-camera"></span> TAKE PHOTO</button>
	
			</div>
	</div>

		<hr>

		<div class="s">
			<div class="photoArea"></div>
			<video style="width: 400px; height: 280px;" id="video" muted controls></video>
			<canvas style="width: 233px;" id="canvas"></canvas>
		</div>
		

		<script id="jsbin-javascript">
		(function() {

		  var streaming = false,
			  video        = document.querySelector(\'#video\'),
			  canvas       = document.querySelector(\'#canvas\'),
			  photo        = document.querySelector(\'#photo\'),
			  startbutton  = document.querySelector(\'#startbutton\'),
			  width = 640,
			  height = 480;

		  navigator.getMedia = ( navigator.getUserMedia ||
								 navigator.webkitGetUserMedia ||
								 navigator.mozGetUserMedia ||
								 navigator.msGetUserMedia);

		  navigator.getMedia(
			{
			  video: true,
			  audio: false
			},
			function(stream) {
			  if (navigator.mozGetUserMedia) {
				video.mozSrcObject = stream;
			  } else {
				var vendorURL = window.URL || window.webkitURL;
			
				try {
				  video.srcObject = stream;
				} catch (error) {
				  video.src = URL.createObjectURL(stream);
				}

			  }
			  video.play();
			},
			function(err) {
			  console.log("An error occured! " + err);
			}
		  );

		  video.addEventListener(\'canplay\', function(ev){
			if (!streaming) {
			  height = video.videoHeight / (video.videoWidth/width);
			  video.setAttribute(\'width\', width);
			  video.setAttribute(\'height\', height);
			  canvas.setAttribute(\'width\', width);
			  canvas.setAttribute(\'height\', height);
			  streaming = true;
			}
		  }, false);
		 
		  function takepicture() {
			canvas.width = 400;
			canvas.height = height; 
			canvas.getContext(\'2d\').drawImage(video, 120, 0, height, width, 0, 0, height, width);
			var data = canvas.toDataURL(\'image/png\');



			image = data.replace(\'data:image/png;base64,\', \'\');
			var number = $("#number").val();
			$("#number").val("");

			jQuery.ajax({
			type: \'POST\',
			url: \''.$url.'\',
			dataType: \'json\',
			data: ({
				number: number,
				data : image
				}),
			success: function (msg) {
				alert("Uploaded successfully");
			}
			});

		
			alert("PHOTO CAPTURED!");
		  }

		  startbutton.addEventListener(\'click\', function(ev){
			  takepicture();
			ev.preventDefault();
		  }, false);

		})();
		</script>';

	}
}
?>
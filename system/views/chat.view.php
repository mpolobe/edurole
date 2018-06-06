<?php
class chat{

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
	}


	public function startChat($item){
		$sender = $this->core->userID;

		if (file_exists("datastore/identities/pictures/$recipient.png")) {
			$ravatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$recipient.'.png';
		} else {
			$ravatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
		}

		if (file_exists("datastore/identities/pictures/$sender.png")) {
			$savatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$sender.'.png';
		} else {
			$savatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
		}

		echo'<script>
			$(function() {
				$("#messages").animate({ scrollTop: $("#messages").prop("scrollHeight")}, 0);

				$(document).on("keypress", "[id^=input]", function (e) {
		
					if (e.keyCode == 13) { 

						var message = $(this).val();
						var name = $(this).attr("id").substring(5); 
        
						$.ajax({
							type: "GET",
							url: "'.$this->core->conf['conf']['path'].'/api/chat/send",
							data: { "msg" : message,
							"uid" : name },
							dataType: "json",
							success: function(data){
								$("#messages"+name).append("<div class=\"row msg_container base_sent\"><div class=\"gavatar\"><img src=\"'.$savatar.'\" class=\" img-responsive \"></div><div class=\"col-md-10 col-xs-10\"><div class=\"messages msg_sent\"><p>" + data.message + "</p><time datetime=\"" + data.date + "\">You - " + data.date + "</time></div></div></div>");
								$("#input"+name).val("");
								$("#messages"+name).animate({ scrollTop: $("#messages"+name).prop("scrollHeight")}, 1000);
							}
						});
    					}
 				});


				$(document).on("click", "[id^=userid]", function (e) {

					var name = $(this).attr("id").substring(6); 
	
					$("#title").html(" " + name);
					$("#chatsession").html(\'<div class="panel-body"><div class="msg_container_base" id="messages\' + name + \'"></div></div><div class="panel-footer"><div class="input-group"><input style="width: 260px" id="input\'+ name +\'" name="input\'+ name +\'" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." /><span class="input-group-btn"></span></div></div></div>\');
	
					$(".panel-heading span.icon_minim").removeClass("glyphicon-minus").addClass("glyphicon-arrow-left");

					$.ajax({
						type: "GET",
						url: "'.$this->core->conf['conf']['path'].'/api/chat/history",
						data: { 	"uid" : name },
						dataType: "json",
						success: function(data){
							$("#messages"+name).append(data.messages);
							$("#messages"+name).animate({ scrollTop: $("#messages"+name).prop("scrollHeight")}, 1000);
						}
					});
   
				});

				window.setInterval(function(){
					var num;
					$("[id^=count]").each(function(){
						num = $(this).attr("id").substring(5);
					});

					$.ajax({
						type: "GET",
						url: "'.$this->core->conf['conf']['path'].'/api/chat/check",
						data: { 	"last" : num },
							dataType: "json",
							success: function(data){
								$("#messages"+name).append(data.messages);
								$("#messages"+name).animate({ scrollTop: $("#messages"+name).prop("scrollHeight")}, 1000);
							}
						});

         			 }, 2500);   


				$(document).on("click", "#chatopen", function (e) {
					$("#chatwindow").show();
				});

				$(document).on("click", ".panel-heading span.icon_minim", function (e) {
					var $this = $(this);
					if ($this.hasClass("glyphicon-arrow-left")) {
        					$this.parents(".panel").find("#chats").slideDown();
        					$this.parents(".panel").find("#chatsession").html("");
						$("#title").html(" ONLINE USERS");
        					$this.removeClass("glyphicon-arrow-left").addClass("glyphicon-remove");

						$.ajax({
							type: "GET",
							url: "'.$this->core->conf['conf']['path'].'/api/chat/check",
							data: {	"last" : "home" },
							success: function(data){
								$("#messages"+name).append("<div class=\"row msg_container base_sent\"><div class=\"gavatar\"><img src=\"'.$savatar.'\" class=\" img-responsive \"></div><div class=\"col-md-10 col-xs-10\"><div class=\"messages msg_sent\"><p>" + data.message + "</p><time datetime=\"" + data.date + "\">You - " + data.date + "</time></div></div></div>");
								$("#input"+name).val("");
								$("#messages"+name).animate({ scrollTop: $("#messages"+name).prop("scrollHeight")}, 1000);
							}
						});
					} else {
						$("#chatwindow").hide();
					}
				});
			});
		</script>';

		echo'<div class="chat-window" id="chatwindow">

        	<div class="panel" id="chatpanel">
                <div class="panel-heading top-bar">
                    <div class="col-md-8 col-xs-8">
                        <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span><span id="title"> ONLINE USERS</span></h3>
                    </div>
                    <div class="col-md-4 col-xs-4 chat" style="text-align: right;">
                        <a href="#"><span id="minim_chat_window" class="glyphicon glyphicon-remove icon_minim"> </span></a>
                    </div>
                </div>		
		<div class="chats msg_container_base" id="chats">';


		$sqlc = "SELECT COUNT(`chat`.ID) as Count, `SenderID`, `FirstName`, `Surname` FROM `chat`, `basic-information` 
			WHERE `RecipientID` = '$sender' 
			AND `basic-information`.ID = `SenderID`
			AND `Read` = '0'
			GROUP BY `SenderID`";
		$runc = $this->core->database->doSelectQuery($sqlc);

		while($row = $runc->fetch_assoc()){
			$sender = $row['SenderID'];
			$count = $row['Count'];
			$name = $row['FirstName'] . ' ' . $row['Surname'];
			if($name == ""){ continue; }

			$messages[$sender]=$count;
			$names[$sender]=$name;
		}

		$sql = "SELECT DISTINCT `user`, `FirstName`, `Surname`, `access`.ID as UID FROM `acl`, `access`
			LEFT JOIN `basic-information` ON `basic-information`.ID = `access`.ID
			WHERE `acl`.user = `access`.Username
			AND `access`.ID != '$sender'
			AND `access`.Username NOT REGEXP '^[0-9]+$'
			ORDER BY `basic-information`.Surname ASC LIMIT 2000";

		$run = $this->core->database->doSelectQuery($sql);

		foreach($names as $id=>$name){
			if(!empty($messages[$id])){
				$display = '<div class="mailcount"><b>'.$messages[$id].'</b></div>';
			} else {
				$display = "";
			}

			if($name == " "){ continue; }

			echo '<div class="user" id="userid'.$id.'" style="background-color: #ffe2ad;"><span  class="glyphicon glyphicon-user icon_user"></span><a href="#"> <b>'.$name.' '.$display.'</b></a></div>';
		}

		echo '<div style="border-bottom: 1px solid #999; height: 5px; margin-bottom: 5px;"></div>';

		while($row = $run->fetch_assoc()){
			$username = $row['user'];
			$uid = $row['UID'];
			$name = $row['FirstName'] . ' ' . $row['Surname'];

			if(!empty($messages[$username])){
				$display = '<div class="mailcount"><b>'.$messages[$username].'</b></div>';
			} else {
				$display = "";
			}

			if($name == " "){ continue; }

			echo '<div class="user" id="userid'.$uid.'"><span  class="glyphicon glyphicon-user icon_user"></span><a href="#"> <b>'.$name.' '.$display.'</b></a></div>';
		}



                echo'</div>
		<div id="chatsession">';
		echo'</div>';


		if(isset($_SESSION['lastchat'])){

			$sender = $_SESSION['lastchat'];

			echo'<script>
				$(function() {
					var name = "'.$sender.'"; 
	
					$("#title").html(" " + name);
					$("#chatsession").html(\'<div class="panel-body"><div class="msg_container_base" id="messages\' + name + \'"></div></div><div class="panel-footer"><div class="input-group"><input style="width: 260px" id="input\'+ name +\'" name="input\'+ name +\'" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." /><span class="input-group-btn"></span></div></div></div>\');
	
					$(".panel-heading span.icon_minim").removeClass("glyphicon-minus").addClass("glyphicon-arrow-left");

					$.ajax({
						type: "GET",
						url: "'.$this->core->conf['conf']['path'].'/api/chat/history",
						data: { 	"uid" : name },
						dataType: "json",
						success: function(data){
							$("#messages"+name).append(data.messages);
							$("#messages"+name).animate({ scrollTop: $("#messages"+name).prop("scrollHeight")}, 1000);
						}
					});
				});
			</script>';

		}

    		echo'</div>
        </div>';

	}

}
?>
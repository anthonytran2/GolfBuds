var data;
var thisEmail;
var count = 0;
$.get('php/getSession.php', function(data) {
	data = JSON.parse(data);
	thisEmail = data.email;
});

//Scroll to bottom of chat container.
function scrollBottom(){
	var chatBody = document.getElementById("panel-body");
	chatBody.scrollTop = 100000000000000;
}

//Create chat li and insert to chat container ul.
function chatInsert(time, inputMsg, user, position){ //Position of chat insert, R if this User, L if other.
	//Date.
	var date = new Date(0);
	date.setUTCSeconds(time/1000); //Divde 1000 to convert ms to s.
	//Get chat container.
	var chat = document.getElementById("chat");
	//Create li.
	var li = document.createElement("li");
	$(li).attr({
		class: position+" clearfix"
	});
	//Create span for img and set position.
	var spanImg = document.createElement("span");
	$(spanImg).attr({
		class: "chat-img pull-"+position
	});
	//Create image.
	var img = document.createElement("img");
	$(img).attr({
		class: "img-circle",
		alt: "User Avatar",
		style: "width: 70px; height: 70px;" 
	});
	//Get the image from database.
	//panel-body element to scroll chat to bottom after image loads.
	getProfileImageChat(user, img, document.getElementById("panel-body"));
	//Add image to span.
	spanImg.appendChild(img);
	//Create chat body div.
	var chatBodyDiv = document.createElement("div"); 
	$(chatBodyDiv).attr({
		class: "chat-body clearfix"
	});
	//Create header div.
	var headerDiv = document.createElement("div");
	$(headerDiv).attr({
		class: "header"
	});
	//Create bold name and pull into position.
	var strong = document.createElement("strong");
	if(position === "right") posSeperate = "pull-right";
	else posSeperate = "";
	$(strong).attr({
		class: posSeperate + " " +  "primary-font"
	});
	//Get the name.
	var name = "";
	$.ajax({
		url: "php/getUser.php",
		type: "POST",
		data: {email: user},
		success: function(data) {
			strong.innerHTML = data["FNAME"] + " " + data["LNAME"];
		}, error: function(jqXHR, exception) {
			console.log(jqXHR);
		}
	});
	//Create time and date text and put into position.
	var small = document.createElement("small");
	if(position === "left") posSeperate = "pull-right";
	else posSeperate = "";
	$(small).attr({ 
		class: posSeperate + " text-muted"  
	});
	//Date
	var month = date.getMonth() + 1;
	var day = date.getDate();
	var year = date.getFullYear();
	//Time
	var hour = date.getHours();
	var min = date.getMinutes();
	var zero = "";
	var ampm = "am";
	//Convert time for display.
	if(min < 10) zero = "0"
	if(hour === 0) hour = 12;
	if(hour > 12) {
		hour = hour - 12;
		ampm = "pm";
	}
	//Display time and date.
	small.innerHTML = month + "/" + day + "/" + year + "  -  " + hour + ":" + zero + min + " " + ampm;
	//Show image in position.
	var msg = document.createElement("p");
	msg.style.color = "black";
	msg.innerHTML = inputMsg;
	if(position === "right"){
		headerDiv.appendChild(small);
		headerDiv.appendChild(strong);
	} else { 
		headerDiv.appendChild(strong);
		headerDiv.appendChild(small);
	}				
	//Append all tags together.
	chatBodyDiv.appendChild(headerDiv);
	chatBodyDiv.appendChild(msg);
	li.appendChild(spanImg);
	li.appendChild(chatBodyDiv);
	chat.appendChild(li);			
}

//Handle chat back end functions and display.
function getChat(task, msg) {
	var msgArr = [];
	//Get this user data.
	$.get('php/getSession.php', function(data) {
		var data = JSON.parse(data);
		var userId = data.email;
		
		//Get users group chat url.
		$.get('php/getGroupChat.php', function(data){
			data = JSON.parse(JSON.stringify(data));
			for(var key in data) if(key === "CHATURL") groupUrl = data[key];
												
			$.get('php/getGroupChat.php', function(data){
				//Connect user to Sendbird chat SDK.
				sb.connect(userId, function(user, error) {
					console.log(user, error);
					//Get the channel.
					sb.OpenChannel.getChannel(groupUrl, function(channel, error) {
						if (error) {
							console.error(error);
							return;
						}
						//Enter the channel.
						channel.enter(function(response, error){
							if (error) {
								console.error(error);
								return;
							}
							console.log(response);
						});
						
						//Handle the task given.
						
						//Send message to SendBird.
						if(task === "message") {
							channel.sendUserMessage(msg, null, null, function(message, error){
								if (error) {
									console.error(error);
									return;
								}
								for(var key in message) 
									if(key === "message") retMsg = message[key];
									
								// onSent
								console.log(message);
							});
							scrollBottom();
						}
						
						//Load the channel previous messages.
						if(task === "load") {
							var messageListQuery = channel.createPreviousMessageListQuery();

							//Load 100 messages.
							messageListQuery.load(100, true, function(messageList, error){
								if (error) {
									console.error(error);
									return;
								}
								var array = [];
								//Get message from array of assoc. arr.
								for(var i=messageList.length; i>=0; i--){
									for(var key in messageList[i]){
										//Get sender email.
										if(key === "_sender") {
											 array.push(messageList[i][key]["userId"]);
										}
										//Get message or time.
										if(key === "message" || key === "createdAt") {
											array.push(messageList[i][key]);
										}
										
										//All data recieved.
										if(array.length === 3) {
											if(array[2] === thisEmail) position = "right";
											else position = "left";
											
											chatInsert(array[0], array[1], array[2], position);
											array = [];
										}
									}
									array = [];
								}
                                scrollBottom();
								console.log(messageList);
							});
							
							//Handler to recieve new messages from SendBird.
							var ChannelHandler = new sb.ChannelHandler();
							ChannelHandler.onMessageReceived = function(channel, messageList){
								console.log(channel, messageList);
								
								var array = [];
								for(var key in messageList){
									if(key === "_sender") {
										array.push(messageList[key]["userId"]);
									}
									if(key === "message" || key === "createdAt") {
										array.push(messageList[key]);
									}
									if(array.length === 3) {
										if(array[2] === thisEmail) position = "right";
										else position = "left";

										chatInsert(array[0], array[1], array[2], position);
										array = [];
									}
								}
								scrollBottom();
							};

							sb.addChannelHandler(thisEmail, ChannelHandler);
						}
						
						//Chat closed, user will log off chat.
						if(task === "exit") {
							channel.exit(function(response, error){
								if (error) {
									console.error(error);
									return;
								}
								
								//Hold group ids.
								var groupArr = [];
								//Get group details.
								$.get('php/check_match_detailed.php', function (data) {
									if(data["success"] !== "false") {
										
										data = JSON.parse(JSON.stringify(data));
										var emails = []; 
										var names = [];
										var ids = [];
										var data2 = null;
										
										//Asocc array of assoc arrays.
										for(var key in data){
											if(key === "EMAILS"){
												data2 = data[key];
												for(var key2 in data2){
													emails.push(data2[key2]);
												}
											}
											if(key === "NAMES"){
												data2 = data[key];
												for(var key2 in data2){
													names.push(data2[key2]);
												}
											}
											if(key === "IDS"){
												data2 = data[key];
												for(var key2 in data2){
													ids.push(data2[key2]);
												}
											}
										}
										
										
										for(var key in ids) if(ids[key] !== null) groupArr.push(ids[key]);
										//Show all users as offline for this user when chat closed.
										for(var i=0; i<groupArr.length; i++) online = document.getElementById(groupArr[i]).style.backgroundColor = "#494949";
									}
								});
								console.log(response);
							});	
						}
						
						//Show users online status.
						if(task === "status"){
							var participantListQuery = channel.createParticipantListQuery();
							participantListQuery.next(function (participantList, error) {
								if (error) {
									console.error(error);
									return;
								}
								
								var arr = [];
                                var online;
								var groupArr = [];
								//Get group data.
								$.get('php/check_match_detailed.php', function (data) {
									if(data["success"] !== "false") {
										data = JSON.parse(JSON.stringify(data));
										var emails = [];
										var names = [];
										var ids = [];
										var data2 = null;
										
										//Assoc array of assoc array.
										for(var key in data){
											if(key === "EMAILS"){
												data2 = data[key];
												for(var key2 in data2){
													emails.push(data2[key2]);
												}
											}
											if(key === "NAMES"){
												data2 = data[key];
												for(var key2 in data2){
													names.push(data2[key2]);
												}
											}
											if(key === "IDS"){
												data2 = data[key];
												for(var key2 in data2){
													ids.push(data2[key2]);
												}
											}
										}
									
										//Array of users set up.
										for(var key in ids) if(ids[key] !== null) groupArr.push({"id":ids[key], "hit":0, "email": emails[key]});
										
										//No one online.
										if(participantList.length === 0) {
											for(var j=0; j<groupArr.length; j++){
												if(arr[0] === groupArr[j]["email"]) { 
													online = document.getElementById(groupArr[j]["id"]);
													online.style.backgroundColor = "#494949";   
												} 
											}	
										} else { //Someone is online.
											for(var i=participantList.length; i>=0; i--){
												for(var key in participantList[i]) {
													if(key === "connectionStatus") {
														arr.push(participantList[i][key]);
													} 
													if(key === "userId") { //Email.
														arr.push(participantList[i][key]);
													}
													
													//All data extracted.
													if(arr.length === 2) {
														var found = false;
														online = document.getElementById(arr[0]);

														//Incr hit is online from Sendbird.
														for(var j=0; j<groupArr.length; j++){
															if(arr[0] === groupArr[j]["email"]) { 
																groupArr[j]["hit"] = groupArr[j]["hit"]+1; 
															} 
														}
																								
                                                        //Display online or offline based on hits.																							
														for(var y=0; y < groupArr.length; y++){
														   for(var b in groupArr[y]){
															   //Online else offline.
															   if(b === "hit" && groupArr[y][b] > 0) {
																   online = document.getElementById(groupArr[y]["id"]);
																   online.style.backgroundColor = "#00cc3d";
															   } else if(b === "hit" && groupArr[y][b] === 0) {
																   online = document.getElementById(groupArr[y]["id"]);
																   online.style.backgroundColor = "#494949";   
															   }
														   }
													    }
														//Clear array after each user.
													    arr = [];
													}
												}
												//Clear array.
												arr = [];
											}
										}
									}
								});
								console.log(participantList);
							});
						}
						console.log(channel);
					});
				});
			});
		});
	});	
}
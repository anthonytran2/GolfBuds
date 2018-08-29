function getName(value){
	var name = "";
	$.ajax({
		url: "php/getUser.php",
		type: "POST",
		data: {email: value},
		async: false,
		success: function(data) {
			name = data["FNAME"] + " " + data["LNAME"];
		}, error: function(jqXHR, exception) {
			console.log(jqXHR);
		}
	});
	return name;
}

function getMatch(emails, names, ids) {	
	var container, card, image, name, link = "";
	var groupSize = 0;
	var userIds = [];

	for(var key in emails) {
		if(emails[key] != null) {
			userIds.push(emails[key]);

			container = document.getElementById("matchRow");
			card = document.createElement("div");
			$(card).attr({
				class: "card matchCard col-md-3",
				style: "padding: 0px;"
			});
			image = document.createElement("img");
			$(image).attr({
			   width: "100%",
			   height: "100%"
			});
			getProfileImage(emails[key], image);
			card.appendChild(image);
			var online = document.createElement("div");
			$(online).attr({
				class: "dot",
				style: "margin-left: 91%; margin-right: 9%; margin-top: 25px;",
				id: ids[key]
			});
			name = document.createElement("h4");
			$(name).attr({
			   class: "matchName"
			});
			
			link = document.createElement("a");
			$(link).attr({
				class: "profileLink",
				"data-value" : ids[key] 
			});
			//Profile click.
			link.onclick = function() {
				var value =  $(this).data("value");
				$.ajaxSetup({cache: false})
				$.ajax({
					url: "php/getEmailFromId.php",
					type: "POST",
					data: { id: value },
					success: function(retVal) {
						
						$.ajaxSetup({cache: false})
						$.ajax({
							url: "php/setProfileSession.php",
							type: "POST",
							data: { email: retVal["EMAIL"] },
							success: function(ret) {
								 window.location.href = "viewProfile.html";
							},
							error: function(jqXHR, exception) {
								console.log(jqXHR);
							}
						});
					},
					error: function(jqXHR, exception) {
						console.log(jqXHR);
					}
				});
			};

			name.innerHTML = names[key];
			name.appendChild(online);
			card.appendChild(name);
			link.appendChild(card);
			container.appendChild(link);
			
			groupSize++;
		}
	}
	var array = [userIds, groupSize];
	return array;
}
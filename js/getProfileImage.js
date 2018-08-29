//Get image and set image src to given img tag.
function getProfileImage(email, img) {
	var link = "";
	var defaultImg = "../images/default.png"; 
	$.ajax({
		url: "php/getProfileImage.php", 
		data: {user: email},
		type: "POST", 
		success: function(data){
			link = data["MSG"];
			if(link === null) link = defaultImg;
			$(img).attr("src", link);
		},
		error: function(jqXHR, exception) {
			console.log(jqXHR, exception);
		}
	});
}

//Get image for chat.
function getProfileImageChat(email, img, chat) {
	var link = "";
	var defaultImg = "../images/default.png"; 
	$.ajax({
		url: "php/getProfileImage.php", 
		data: {user: email},
		type: "POST", 
		success: function(data){
			link = data["MSG"];
			if(link === null) link = defaultImg;
			$(img).attr("src", link);
			chat.scrollTop = 1000000; //Scroll to bottom of chat.
		},
		error: function(jqXHR, exception) {
			console.log(jqXHR, exception);
		}
	});
}


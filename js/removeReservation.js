function removeReservation() {
	$.ajax({
		url: "php/removeReservation.php",
		type: "GET",
		success: function(ret) {
			 if(ret["error"]) {
				var out = "";
				var arr = ret["error"];
				for(var i=0, len = arr.length; i<len; i++){
					out += " " + arr[i] + "\n";
				}
				alert(out);
			 } else {
				document.getElementById("reserveTable").deleteRow(1);
				var node = document.getElementById("matchRow");
				
				while (node.hasChildNodes()) {
					node.removeChild(node.lastChild);
				}
				var chatDropdown = document.getElementById("chatContainer").style.display = "none";
				var container = document.getElementById("matchRow");
				document.getElementById("submitReserve").style.display = "block";
				document.getElementById("groupSizeTitle").innerHTML = "";
				if( $("#chatButton").attr('aria-expanded') === "true") $("#chatButton").click();
			 }
		 }, 
		 error: function(jqXHR, exception) {
			 console.log(jqXHR);
		 }
	});
}
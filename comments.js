// Gets the path to the directory in which the Javascript file is located
$path = document.getElementById("sc_script").getAttribute("src").slice(0, -11);

function display() {
	// To completely remove the standard CSS, comment or delete the line below
	basicStyle();

	// Get the sc_main div, and store it as parent
	var parent = document.getElementById("sc_main");

		// Create the comment submission form
		var form = document.createElement("form");
		form.setAttribute("id", "sc_comment_form");
		form.setAttribute("method", "POST");
		form.setAttribute("action", "");
		form.setAttribute("accept-charset", "utf-8");

		var nameField = document.createElement("input");
		nameField.setAttribute("id", "sc_name_field");
		nameField.setAttribute("type", "text");
		nameField.setAttribute("name", "name");

		var emailField = document.createElement("input");
		emailField.setAttribute("id", "sc_email_field");
		emailField.setAttribute("type", "text");
		emailField.setAttribute("name", "email");

		var hideBox = document.createElement("input");
		hideBox.setAttribute("id", "sc_hide_mail");
		hideBox.setAttribute("type", "checkbox");
		hideBox.setAttribute("name", "hidemail");
		hideBox.setAttribute("checked", "true");

		var messageField = document.createElement("textarea");
		messageField.setAttribute("id", "sc_message_field");
		messageField.setAttribute("name", "message");
		messageField.setAttribute("cols", "60");
		messageField.setAttribute("rows", "5");

		var submitButton = document.createElement("input");
		submitButton.setAttribute("id", "sc_submit_comment");
		submitButton.setAttribute("type", "button");
		submitButton.setAttribute("value", "Add Comment");
		submitButton.setAttribute("onClick", "submitComment(this.form)");

		form.innerHTML = "Name: ";
		form.appendChild(nameField);
		form.innerHTML += " Email: ";
		form.appendChild(emailField);
		form.appendChild(hideBox);
		form.innerHTML += "Hide Email <br> Comment: <br>";
		form.appendChild(messageField);
		form.innerHTML += "<br>";
		form.appendChild(submitButton);

	// Add the comment submission form to the sc_main div
	parent.appendChild(form);

		// Populate the comments
		var commentsDiv = document.createElement("div");
		commentsDiv.setAttribute("id", "sc_comments");

		var payload = new FormData();
		payload.append("displayCall", true);
		payload.append("url", document.URL);

		var request = new XMLHttpRequest();
		request.open("POST", $path+"comments.php", true);
		request.send(payload);
		request.onload = function(e) {
			commentsDiv.innerHTML += request.responseText;
		}

	// Add the comments div to the sc_main div
	parent.appendChild(commentsDiv);
}

function basicStyle() {
	var style = document.createElement("style");
	style.innerHTML = ".sc_comment{position:relative;} .sc_comment_body{" +
	"display:inline-block;margin-left:90px;} .sc_date{padding:0;margin:0;}" +
	".sc_name{padding:0;margin:0;} .sc_avatar{position:absolute;top:50%;" +
	"margin-top:-40px;}"
	document.head.insertBefore(style, document.head.firstChild);
}

function submitComment(form) {
	var payload = new FormData(form);
	payload.append("url", document.URL);

	var request = new XMLHttpRequest();
	request.open("POST", $path+"comments.php", true);
	request.send(payload);
	request.onload = function(e) {
		console.log(request.responseText);
		var response = JSON.parse(request.responseText);

		if (response.valid) {
			document.getElementById("sc_comment_form").innerHTML = response.msg;
		}
		else {
			window.alert(response.msg);
		}
	}
}

// Wait until the page has finished loading, then run the functions
window.addEventListener("DOMContentLoaded", function(f) {
	display();
})
function display() {

	// Get the simple_comments div, and store it as parent
	var parent = document.getElementById("simple_comments");

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
		submitButton.setAttribute("type", "submit");
		submitButton.setAttribute("value", "Add Comment");

		form.innerHTML = "Name: ";
		form.appendChild(nameField);
		form.innerHTML += " Email: ";
		form.appendChild(emailField);
		form.appendChild(hideBox);
		form.innerHTML += "Hide Email <br> Comment: <br>";
		form.appendChild(messageField);
		form.innerHTML += "<br>";
		form.appendChild(submitButton);

	// Add the comment submission form to the simple_comments div
	parent.appendChild(form);
}


// Wait until the page has finished loading, then run the functions
window.addEventListener("DOMContentLoaded", function(f) {
	display();
})
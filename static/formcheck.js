//Created by Script-Smart (www.script-smart.com)
//Form field checker

var form = "";
var submitted = false;
var error = false;
var error_message = "";


function check_checkbox(field_name, message) {
  var isChecked = false;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name];

    if (field_value.checked == true) {
      isChecked = true;
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }

  }
}

function check_input(field_name, field_size, message, max_field_size) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size || field_value.length > max_field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_radio(field_name, message) {
  var isChecked = false;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var radio = form.elements[field_name];

    for (var i=0; i<radio.length; i++) {
      if (radio[i].checked == true) {
        isChecked = true;
        break;
      }
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_select(field_name, field_default, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == field_default) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_password(field_name_1, field_name_2, field_size, message_1, message_2) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

    if (password == '' || password.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password != confirmation) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    }
  }
}

function check_password_new(field_name_1, field_name_2, field_name_3, field_size, message_1, message_2, message_3) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password_current = form.elements[field_name_1].value;
    var password_new = form.elements[field_name_2].value;
    var password_confirmation = form.elements[field_name_3].value;

    if (password_current == '' || password_current.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password_new == '' || password_new.length < field_size) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    } else if (password_new != password_confirmation) {
      error_message = error_message + "* " + message_3 + "\n";
      error = true;
    }
  }
}

function check_email(field_name, field_size, message)
{
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    var blnRetval, intAtSign, intDot, intComma, intSpace, intLastDot, intDomain, intStrLen;

	intAtSign=field_value.indexOf("@");
	intDot=field_value.indexOf(".",intAtSign);
	intComma=field_value.indexOf(",");
	intSpace=field_value.indexOf(" ");
	intLastDot=field_value.lastIndexOf(".");
	intDomain=intDot-intAtSign;
	intStrLen=field_value.length;

	// *** CHECK FOR BLANK EMAIL VALUE
    if (field_value == '' || field_value.length < field_size) {
		error_message = error_message + "* " + message + "\n";
		error = true;
    }
	// **** CHECK FOR THE  @ SIGN?
	else if (intAtSign == -1)
	{
		alert("Your email address is missing the \"@\".");
		form.elements[field_name].focus();
		error = true;
	}
	// **** Check for commas ****
	else if (intComma != -1)
	{
		alert("Email address cannot contain a comma.");
		form.elements[field_name].focus();
		error = true;
	}
	// **** Check for a space ****
	else if (intSpace != -1)
	{
		alert("Email address cannot contain spaces.");
		form.elements[field_name].focus();
		error = true;
	}
	// **** Check for char between the @ and dot, chars between dots, and at least 1 char after the last dot ****
	else if ((intDot <= 2) || (intDomain <= 1)  || (intStrLen-(intLastDot+1) < 2))
	{
		alert("Please enter a valid Email address.\n" + field_value + " is invalid.");
		form.elements[field_name].focus();
		error = true;
	}
  }
}

function check_email_confirm(field_name_1, field_name_2, message) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var email = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

	if (email != confirmation) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_entryform(form_name) {

  error = false;
  form = form_name;
  error_message = "";

  check_input("usersname", 2, "You must enter your Name");
  check_email("usersemail", 2, "You must enter your valid Email Address");
  check_input("friendsname", 2, "You must enter a friends Name");
  check_email("friendsemail", 2, "You must enter a friends valid Email Address");

  if (error == true) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }

}


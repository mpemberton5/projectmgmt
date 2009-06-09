function InitClient(cmd) {
	view('divGeneral');
}

function InitClientORG(cmd) {
	var frm = document.forms["frm"];

	var readOnly = (cmd == "" || cmd == "view");
	if (readOnly) {
		for (var i = 0; i < divs.length; i++) {
			var div = document.getElementById(divs[i]);
			if (div != null && cmd == "") {
				div.style.display = "";
			}
		}
	}
	view('divGeneral');
}

function AddClient() {
	var win = window.open("ClientInfo.aspx?cmd=new", "winClientEditor", centerWin(550,600));
	win.focus();
}

function EditClient(shortname) {
	var win = window.open("ClientInfo.aspx?cmd=edit&grp_id=" + shortname, "winClientEditor", centerWin(550,600));
	win.focus();
}

function EditUsers(shortname) {
	var win = window.open("UserList.aspx?grp_id=" + shortname, "winClientEditor", centerWin(550,600));
	win.focus();
}

function ValidateUserConfig(shortname) {
	var win = window.open("ValidateUserConfig.aspx?shortname=" + shortname, "winClientEditor", centerWin(550,400));
	win.focus();
}

var divs = new Array("divGeneral", "divOrganization", "divTelephones", "divGroups");
var divsOrg = new Array("divGeneral", "divContact", "divAddresses", "divLocations", "divTechnical", "divPermissions", "divStyles");

function InitUser(cmd) {

	var frm = document.forms["frm"];
	if (cmd == "useredit") {
		top.fraHeader.ChangeTitle("Change User Options");
	}
	var readOnly = (cmd == "" || cmd == "view");
	if (readOnly) {
		for (var i = 0; i < divs.length; i++) {
			var div = document.getElementById(divs[i]);
			if (div != null && cmd == "") {
				div.style.display = "";
			}
		}
	}
	view('divGeneral');
}

function initOrg(readOnly) {
	var frm = document.forms["frm"];
	if (readOnly) {
		for (var i = 0; i < divsOrg.length; i++) {
			var div = document.getElementById(divsOrg[i]);
			if (div != null) {
				div.style.display = "";
			}
		}
	}
	for (var i = 0; i < frm.elements.length; i++) {
		var e = frm.elements[i];

		e.disabled = false;
		if (e.type == "text" || e.tagName == "TEXTAREA") {
			if (!e.alwaysReadOnly)
				e.readOnly = readOnly;
		} else if (e.type == "checkbox" || e.type == "radio") {
			e.disabled = readOnly;
		}
	}
	view('divGeneral');
}

function updateDisplayName() {
	var frm = document.forms["frm"];
	if (frm.cbAutoFill.checked) {
		var FirstName = trim(frm.FirstName.value);
		var LastName = trim(frm.LastName.value);
		var name = FirstName;
		if (LastName != "") name += " " + LastName;

		frm.DisplayName.value = trim(name);
	}
}

function AutoFillClicked() {
	var frm = document.forms["frm"];
	if (frm.cbAutoFill.checked) {
		updateDisplayName();
		frm.DisplayName.style.backgroundColor = "#E0E0E0";
		frm.DisplayName.readOnly = true;
	} else {
		frm.DisplayName.style.backgroundColor = "";
		frm.DisplayName.readOnly = false;
	}
}

function trim(s) {
	return s.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

// user
function edit() {
	var win = window.open("default.aspx?cmd=edit", "winUserEditor", centerWin(500,500));
	win.focus();
}

function save() {
	var frm = document.forms["frm"];
	
	var errs = "";

	if (frm.upnPrefix.value == "") errs += "User name is required\n";
	if (frm.givenName.value == "") errs += "First name is required\n";
	if (frm.sn.value == "") errs += "Last name is required\n";
	if (frm.displayName.value == "") errs += "Display name is required\n";
	
	// if password box present, verify those too
	if (frm.newpassword) {
		if (frm.newpassword.value == "" || frm.confirmpassword.value == "") {
			errs += "Password is required\n";
		} else if (frm.newpassword.value != frm.confirmpassword.value) {
			errs += "The New and Confirm passwords must match\n";
		}
	}
	
	if (errs == "") {
		document.body.style.cursor = "wait";
		frm.submit();
	} else {
		alert(errs);
	}
}

// organization
function editOrg(shortname) {
	var win = window.open("ClientInfo.aspx?cmd=edit&shortname=" + shortname, "winOrgEditor", centerWin(550,600));
	win.focus();
}

function saveOrg() {
	var frm = document.forms["frm"];
	document.body.style.cursor = "hand";
	frm.submit();
}

function password() {
	var url = "../admin/dialog.aspx?cmd=password";
	var win = window.open(url, "winDialog", centerWin(400, 350));
	win.focus();
}

//------------------------------------
// group management functions
//------------------------------------

// enabled/disables remove button
// each time selection in list changes
function grouplist_update_ui() {
	var list = document.getElementById("groupList");
	if (!list) return;
	
	var index = list.selectedIndex;
	
	if (index == -1) {
		if (list.options.length == 0) return;
		list.selectedIndex = 0;
		index = 0;
	}

	var option = list.options[index];

	var btn = document.getElementById("btnRemoveUserFromGroup");
	if (!btn) return;
		
	// don't remove from AllUsers
	btn.disabled = option.value.indexOf("AllUsers@") == 0;
}

function permission_submit() {
	var list = document.getElementById("selList");
	var form = document.forms["frmPermission"];
	var newPerms = form.elements["new_permissions"];
	
	// store new permissions as
	// username|permission;...
	
	newPerms.value = "";
	for (var i = 0; i < list.options.length; i++) {
		var option = list.options[i];
		newPerms.value += option.value + "|" + option.permissions + ";";
	}
	return true;
}

function grouplist() {
	var url = "../efile/dialog.aspx?cmd=userlist&template=../lib/templates/userlist&filter=group";
	var value = window.showModalDialog(url, null, "scroll:no;status:no;dialogHeight:21;dialogWidth:22");
	
	return value == null ? null : value.split("|");
}

function addUserToGroup() {
	var list = document.getElementById("groupList");
	
	var value = grouplist(); //prompt("Enter group name:", "");
	//alert(value);
	if (value == null || value == "undefined") return;
	
	// don't add dupes
	for (var i = 0; i < list.options.length; i++) {
		if (list.options[i].value == value[0]) return;
	}
	
	var option = document.createElement("OPTION");

	option.value = value[0];
	option.text = value[1];

	list.add(option);
	option.selected = true;
	grouplist_update_ui();

	group_update_list("add", "group_add_list", option.value);
	group_update_list("remove", "group_remove_list", option.value);
}

// remove selected item from list and add value to hidden remove_list
function removeUserFromGroup() {
	var list = document.getElementById("groupList");
	var index = list.selectedIndex;
	
	if (index == -1) return;
	var option = list.options[index];
	
	// don't remove AllUsers
	if (option.value.indexOf("AllUsers@") == 0) return;
	
	group_update_list("add", "group_remove_list", option.value);
	group_update_list("remove", "group_add_list", option.value);
	
	list.removeChild(option);
	list.selectedIndex = 0;	
	grouplist_update_ui();
}

function group_update_list(action, listname, item) {
	var form = document.forms["frm"];
	var list = form.elements[listname];

	var index = list.value.indexOf(item + ";");
	if (action == "add") {
		if (index == -1) {
			list.value += item + ";"
		}
	} else if(action == "remove") {
		if (index != -1) {
			list.value = list.value.substring(0, index) + list.value.substring(index + item.length + 1);
		}
	}
}

function userlist() {
	var url = "../efile/dialog.aspx?cmd=userlist&template=../lib/templates/userlist&filter=user";
	var value = window.showModalDialog(url, null, "scroll:no;status:no;dialogHeight:21;dialogWidth:22");

	return value == null ? null : value.split("|");
}

function addMemberToGroup() {

	var list = document.getElementById("groupList");
	
	var value = userlist();   //prompt("Enter group name:", "");
	if (value == null || value == "undefined") return;
	
	// don't add dupes
	for (var i = 0; i < list.options.length; i++) {
		if (list.options[i].value == value[0]) return;
	}
	
	var option = document.createElement("OPTION");
	
	option.value = value[0];
	option.text = value[1];

	list.add(option);
	option.selected = true;
	userlist_update_ui();
	
	user_update_list("add", "user_add_list", option.value);
	user_update_list("remove", "user_remove_list", option.value);
}

// remove selected item from list and add value to hidden remove_list
function removeMemberFromGroup() {
	var list = document.getElementById("groupList");
	var index = list.selectedIndex;

	if (index == -1) return;
	var option = list.options[index];

	// don't remove AllUsers
	if (option.value.indexOf("AllUsers@") == 0) return;

	user_update_list("add", "user_remove_list", option.value);
	user_update_list("remove", "user_add_list", option.value);

	list.removeChild(option);
	list.selectedIndex = 0;	
	userlist_update_ui();
}

function user_update_list(action, listname, item) {
	var form = document.forms["frm"];
	var list = form.elements[listname];
	var index = list.value.indexOf(item + ";");

	if (action == "add") {
		if (index == -1) {
			list.value += item + ";"
		}
	} else if(action == "remove") {
		if (index != -1) {
			list.value = list.value.substring(0, index) + list.value.substring(index + item.length + 1);
		}
	}
}

function userlist_update_ui() {

}

var divCurrent = null;

function view(divName) {
	if (divCurrent != null) {
		divCurrent.style.display = "none";
	}
	var div = document.getElementById(divName);
	if (div == null) return;
	divCurrent = div;
	
	divCurrent.style.display = "";

	updateUI(divName);	
}

function updateUI(divName) {
	var btnBack = document.getElementById("btnBack");
	if (!btnBack) return;
	
	var btnNext = document.getElementById("btnNext");

	var i = findDiv(divName);

	if (i == 0) {
		btnBack.disabled = true;
	} else if (i == divs.length - 1) {
		btnBack.disabled = false;
		btnNext.value = "Finish";
	}	else {
		btnBack.disabled = false;
		btnNext.value = "Next >";
	}
}

function back() {
	var i = findDiv(divCurrent.id);
	view(divs[i - 1]);
}

function next() {
	var i = findDiv(divCurrent.id);
	if (i == divs.length - 1) {
		save();	
	} else {
		view(divs[i + 1]);
	}
}

function findDiv(divName) {
	for (var i = 0; i < divs.length; i++) {
		if (divs[i] == divName) {
			return i;
		}
	}
	return -1;
}

//======================
// user list functions
// userlist is a modal dialog
//======================
function userlist_ok() {
	var retval = null;
	var list = document.getElementById("selList");
	var index = list.selectedIndex;
	
	if (index != -1) {
		var option = list.options[index];
		retval = option.value + "|" + option.text;
	}
	window.returnValue = retval;
	window.close();
}

//==========================
// user management functions
//==========================
function addUser() {
	alert("Option Not Available Yet.  Please call the Helpdesk for more information.");
	//var win = window.open("NewUserGroup.aspx?type=user", "winUserEditor", centerWin(400,250));
	//win.focus();
}

function editUser(userNameA,groupName) {
	if (groupName == "") {
		var win = window.open("EditUser.aspx?cmd=adminedit&username=" + userNameA, "winUserEditor", centerWin(500,500));
		win.focus();
	} else {
		var win = window.open("EditUser.aspx?cmd=adminedit&username=" + userNameA + "&grp_id=" + groupName, "winUserEditor", centerWin(500,500));
		win.focus();
	}
}

function viewUser(userNameA) {
	var win = window.open("EditUser.aspx?cmd=view&username=" + userNameA, "winUserEditor", centerWin(500,500));
	win.focus();
}

function inactivateUser(userName,groupName) {
	var win = window.open("UserList.aspx?Status=InactivateUser&UserName=" + userName + "ClientID=" + groupName, "winGroupEditor", centerWin(400,400));
	win.focus();
}

function deleteUser(userName) {
	if (confirm("Are you sure you want to delete the user " + userName)) {
		if(confirm("WARNING THIS WILL PERMANANTLY DELETE THE USER!!\n\n                        ARE YOU SURE?")) {
			var url = "./user.aspx?cmd=delete&name=" + userName;
			execCmd(url);
		}
	}
}

function resetPassword(userName, path, groupName) {
	if (groupName == "") {
		var url = path + "ResetPassword.aspx?username=" + userName;
		var win = window.open(url, "winDialog", centerWin(400, 250));
		win.focus();
	} else {
		var url = path + "ResetPassword.aspx?username=" + userName + "&grp_id=" + groupName;
		var win = window.open(url, "winDialog", centerWin(400, 250));
		win.focus();
	}
}

//===========================
// group management functions
//===========================
function addGroup() {
	var win = window.open("NewUserGroup.aspx?type=group", "winGroupEditor", centerWin(400,250));
	win.focus();
}

function editGroup(groupName) {
	var win = window.open("EditGroup.aspx?cmd=edit&name=" + groupName, "winGroupEditor", centerWin(400,400));
	win.focus();
}

function deleteGroup(groupName) {
	if (confirm("Are you sure you want to delete the group - " + groupName)) {
		var url = "EditGroup.aspx?cmd=delete&name=" + groupName;
		execCmd(url);	
	}
}

//==============================
// location management functions
//==============================
function addLocation() {
	var win = window.open("NewUserGroup.aspx?type=location", "winLocEditor", centerWin(400,250));
	win.focus();
}

function editLocation(locationName) {
	var win = window.open("EditLocation.aspx?cmd=edit&LocID=" + locationName, "winLocEditor", centerWin(400,400));
	win.focus();
}

function deleteLocation(locationName) {
	if (confirm("Are you sure you want to delete this Location - " + locationName)) {
		var url = "EditLocation.aspx?cmd=delete&LocID=" + locationName;
		execCmd(url);	
	}
}

function execCmd(url) {
	var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttp.Open("POST", url, false);
	xmlhttp.setRequestHeader("Content-type:", "application/x-www-form-urlencoded");
	xmlhttp.Send("IsPostBack=true");
 
	var status = xmlhttp.responseText;
	if (status != "Success" && trim(status) != "") {
		alert(status);
	}
	document.location.reload();
}

function checkGroupName(groupName) {

}

function checkAllPermissions(checked) {
	var form = document.forms["frm"];
	
	for (var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if (e.type == "checkbox" && e.name.substr(0, 7) == "access-") {
			e.checked = checked;
		}
	}
}

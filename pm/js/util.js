//===============================================================
// NewWindow
//===============================================================
var win = null;

function NewWindow(mypage,myname,w,h,scroll){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings = 'height='+h+',';
  settings += 'width='+w+',';
  settings += 'top='+wint+',';
  settings += 'left='+winl+',';
  settings += 'scrollbars='+scroll+',';
  settings += 'resizable=yes';
  win = window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}

function windowOpener(url, name, args) {
	if (typeof(popupWin) != "object"){
		popupWin = window.open(url,name,args);
	} else {
		if (!popupWin.closed){ 
			popupWin.location.href = url;
		} else {
			popupWin = window.open(url, name,args);
		}
	}
	popupWin.focus();
}

function Button_Link(toref) {
 top.location = toref
}

function doScrollTabs(object) {
	var daScrollUp = document.all.item("daScrollUp");
	var daScrollDown = document.all.item("daScrollDown");
	//alert(document.body.clientHeight);
	if(document.body.clientHeight < 556)
	{
		daScrollUp.style.top = document.body.clientHeight-(2*daScrollDown.clientHeight)-16;
		daScrollDown.style.top = document.body.clientHeight-daScrollDown.clientHeight-8;
	}
	else
	{
		daScrollUp.style.top = -200;
		daScrollDown.style.top = -200;
	}
}

function redoScrollTabs(object) {
	var daScrollUp = document.all.item("daScrollUp");
	var daScrollDown = document.all.item("daScrollDown");
	//alert(document.body.scrollTop);
	daScrollUp.style.top = (document.body.clientHeight-(2*daScrollDown.clientHeight)-16)+document.body.scrollTop;
	daScrollDown.style.top = (document.body.clientHeight-daScrollDown.clientHeight-8)+document.body.scrollTop;
}

function mail(folder) {
	if (typeof folder == "undefined") folder = "";
	var win = window.open("mail/?folder=" + folder, "winMail", "resizable=yes");
	win.focus();
}

function mailmsg(form) {
	var win = window.open("mail/" + form + ".aspx?cmd=new", "", centerWin(700,500) + ",resizable=yes");
	win.focus();
}

function searchType_OnChange() {
	var searchType = document.getElementById("searchType").value;

	if (searchType == "zip" || searchType == "icd9")
	{
		search();
	}
}

function search() {
	var searchType = document.getElementById("searchType").value;
	var searchQuery = document.getElementById("searchQuery").value;
	
	var theFrame = top.frames["fraRightFrame"];
	
	var url;
	switch (searchType)
	{
		case "web":
			url = "http://www.google.com/search?q=" + searchQuery;
		break;
		case "people":
			
		break;
		case "zip":
			url = "http://www.usps.gov/ncsc/lookups/lookup_zip+4.html"
		break;
		case "icd9":
			url = "http://neuro3.stanford.edu/CodeWorrier/FMPro?-db=CodeWorrier&-lay=Detail&-format=search.htm&-view";
		break;
		default:
			return;
	}
	var win = window.open(url);
	win.focus();
		
	document.getElementById("searchQuery").value = "";
}

function logoff() {
	var win = top.frames["fraRightFrame"];
	top.document.location = "logoff.aspx";
}

function displayDiv(divName, optdisplay) {
	var div = document.getElementById(divName);
	
	if (!div) return;
	if (!optdisplay) {
		
		if (div.style.display == "")
		{
			div.style.display = "none";
		}
		else 
		{
			div.style.display = "";
		}
	}
	else 
	{
		div.style.display = optdisplay;
	}
}

function displayDiv2(name) {
	var div = document.getElementById("div" + name);
	var img = document.getElementById("img" + name);

	if (!div) return;

	if (div.style.display == "")
	{
		div.style.display = "none";
		if (img)
		{
			img.src = "images/chevronDown.gif";
			img.title = "Expand";
		}
	}
	else 
	{
		div.style.display = "";
		if (img)
		{
			img.src = "images/chevronUp.gif";
			img.title = "Collapse";
		}
	}
}

function displayDiv3(name) {
	var div = document.getElementById("div" + name);
	var img = document.getElementById("img" + name);

	if (!div) return;
	
	if (div.style.display == "")
	{
		div.style.display = "none";
		if (img)
		{
			img.src = "images/icons/icon-folder.gif";
			img.title = "";
		}
	}
	else 
	{
		div.style.display = "";
		if (img)
		{
			img.src = "images/icons/icon-folder-open.gif";
			img.title = "";
		}
	}
}

function centerWin(width, height) {
	var mainWidth = window.top.document.body.clientWidth;
	var mainHeight = window.top.document.body.clientHeight;
	
	var winLeft = window.top.screenLeft + Math.floor((mainWidth - width) / 2);
	var winTop = window.top.screenTop + Math.floor((mainHeight - height) / 2) - 50;	// account for toolbar, etc.
	
	return "left=" + winLeft + ",top=" + winTop + ",width=" + width + ",height=" + height;
}

//==============================================
// links functions
//==============================================
function addLinkGroup(type) {
	var url = "?cmd=add&type=" + type + "&item=group";
	var win = window.open(url, "winDialog", centerWin(400, 200));
	win.focus();
}

function addLink(type, groupid) {
	var url = "?cmd=add&type=" + type + "&item=link&groupid=" + groupid;
	var win = window.open(url, "winDialog", centerWin(400, 200));
	win.focus();
}

function editLinkGroup(type, id) {
	var url = "?cmd=edit&type=" + type + "&item=group&id=" + id;
	var win = window.open(url, "winDialog", centerWin(400, 200));
	win.focus();
}

function editLink(type, id, groupid) {
	var url = "?cmd=edit&type=" + type + "&item=link&id=" + id + "&groupid=" + groupid;
	var win = window.open(url, "winDialog", centerWin(400, 200));
	win.focus();
}

function saveLink() {
	var frm = document.forms["frm"];
	var url = frm.elements["href"].value;
	
	var re = /http[s]?:\/\/.+/;
	if (! re.test(url))
	{
		alert("Please enter a valid URL.");
		return;
	}
	if(frm.groupid.selectedIndex == 0)
	{
		alert("Please choose a folder.");
		return;
	}
	frm.submit();
}

function saveLinkGroup() {
	var frm = document.forms["frm"];
	frm.submit();
}

function deleteLinkGroup(type, id) {
	if (! confirm("Are you sure you want to delete this folder?\nAll links in this folder will be also deleted."))
	{
		return;
	}
	
	var url = "?cmd=delete&type=" + type + "&item=group&id=" + id;
	window.location = url;
}

function deleteLink(type, id) {
	if (! confirm("Are you sure you want to delete this link?"))
	{
		return;
	}
	
	var url = "?cmd=delete&type=" + type + "&item=link&id=" + id;
	window.location = url;
}

function editLinks(type) {
	var url = "EditTools/links/?cmd=edit&type=" + type;
	window.location = url;
}

//==============================================
// announcement functions
//==============================================
function addAnnouncement(type) {
	var url = "?cmd=add&type=" + type;
	var win = window.open(url, "winDialog", centerWin(500, 500));
	win.focus();
}

function editAnnouncement(type, id) {
	var url = "?cmd=edit&type=" + type + "&id=" + id;
	var win = window.open(url, "winDialog", centerWin(500, 500));
	win.focus();
}

function saveAnnouncement() {
	var frm = document.forms["frm"];
	var editor = document.getElementById("editor");
	
	var memo = frm.elements["memo"];
	memo.value = editor.docsource;
	
	frm.submit();
}

function deleteAnnouncement(type, id) {
	if (! confirm("Are you sure you want to delete this announcement?"))
	{
		return;
	}
	
	var url = "?cmd=delete&type=" + type + "&id=" + id;
	window.location = url;
}

function editAnnouncements(type) {
	var url = "EditTools/Announcements/Default.aspx?cmd=edit&type=" + type;
	window.location = url;
}

function viewAnnouncement(type, id) {
	var url = "EditTools/Announcements/Default.aspx?type=" + type + "&id=" + id;
	var win = window.open(url, "winView", centerWin(500, 500) + ",resizable,scrollbars");
	win.focus();
}

// Window managment functions
function loadWindowPos(idName, storeName) {
		var userData = document.getElementById(idName);
		
		userData.load(storeName);
		var winTop = userData.getAttribute("winTop");
		if (!winTop)
		{
			// make full screen
			window.moveTo(20,20);
			window.resizeTo(screen.availWidth * .80, screen.availHeight * .80);
			
		}
		else 
		{
			var winLeft = userData.getAttribute("winLeft");
			var winHeight = userData.getAttribute("winHeight");
			var winWidth = userData.getAttribute("winWidth");
			
			if (winTop > screen.availHeight) winTop = 20;
			if (winLeft > screen.availWidth) winLeft = 20;
			if (winHeight - winTop > screen.availHeight) winHeight = screen.availHeight - winTop;
			if (winWidth - winLeft > screen.availWidth) winWidth = screen.availWidth - winLeft;
			
			if (winHeight < 100 || winWidth < 100) return;	// keep current size
			//alert("winLeft="+winLeft+"   winTop="+winTop);
			if(winLeft < 0 || winTop<0) return;
			window.moveTo(winLeft, winTop);
			window.resizeTo(winWidth, winHeight);		
		}
}

function saveWindowPos(idName, storeName) {
		var userData = document.getElementById(idName);

		var winTop = getTop();
		var winLeft = getLeft();
		var dims = getSize();
		var winHeight = dims.height;
		var winWidth = dims.width;	
		
		userData.setAttribute("winTop", winTop);
		userData.setAttribute("winLeft", winLeft);
		userData.setAttribute("winHeight", winHeight);		
		userData.setAttribute("winWidth", winWidth);
		userData.save(storeName);
		
}
    
function getInsets() {
   // Store the old document position
   var oldScreenLeft = window.top.screenLeft;
   var oldScreenTop = window.top.screenTop;

   // if no previous inset calculated assume one
   if (window.top._insets == null)
      window.top._insets = {left: 5, top: 80};
   
   // move to a known position
   window.top.moveTo(oldScreenLeft - window.top._insets.left,
                 oldScreenTop - window.top._insets.top);
   
   // Measure the new document position
   var newScreenLeft = window.top.screenLeft;
   var newScreenTop = window.top.screenTop;
   
   // ... and store the insets result
   var res = {
      left:   newScreenLeft - oldScreenLeft + window.top._insets.left,
      top:   newScreenTop - oldScreenTop + window.top._insets.top
   };
   
   // move back the window to its original place
   window.top.moveTo(oldScreenLeft - res.left, oldScreenTop - res.top);
   
   // and backup the insets for next time
   window.top._insets = res;
   
   return res;
}

function getLeft() {
   return window.top.screenLeft - getInsets().left;
}

function getTop() {
   return window.top.screenTop - getInsets().top;
}

function getInnerSize() {
   var el = window.top.document.compatMode == "CSS1Compat" ?
               window.top.document.documentElement :
               window.top.document.body;
   return {
      width:   el.clientWidth,
      height:  el.clientHeight
   };
}

function getSize() {
   // Store old size
   var oldInnerSize = getInnerSize();
   
   // if no previous diff assume one
   if (window.top._diff == null)
      window.top._diff = {width: 10, height: 90};
   
   // resize to known size
   window.top.resizeTo(oldInnerSize.width + window.top._diff.width, oldInnerSize.height + window.top._diff.height);
   
   // calculate inner size again
   var newInnerSize = getInnerSize();
   
   // store diff result
   var diff = {
      width:   oldInnerSize.width - newInnerSize.width + window.top._diff.width,
      height:  oldInnerSize.height - newInnerSize.height + window.top._diff.height
   };
   
   // restore size to old size
   window.top.resizeTo(oldInnerSize.width + diff.width, oldInnerSize.height + diff.height);
   
   // backup diff for future calculations
   window.top._diff = diff;
   
   return {
      width:   oldInnerSize.width + diff.width,
      height:  oldInnerSize.height + diff.height
   };
}
/******************************************************/
function ButtonConfirm(msg, link) {
  if (confirm(msg) == true) {
    window.location.href = link;
    return true;
  } else {
    return false;
  }
}


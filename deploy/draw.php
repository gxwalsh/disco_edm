<?php
include "include.php";


if (isset($_GET['project_id'])) {
	
	$project_id = $_GET['project_id'];
} else {
	$project_id= '4';
}

if (isset($_GET['user_id'])) {
	
	$user_id = $_GET['user_id'];
} else {
	$user_id= '4';
}


if(isset($_GET['viewer'])) {
	$viewer = true;
	} else {
	$viewer = false;
	}

?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: "Lato", sans-serif;
    background-color: "#cccccc";
}

.sidenav {
    height: 100%;
    width: 120;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #999999;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 0px;
}

.sidenav a {
    
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s;
}

.sidenav a:hover {
    color: #f1f1f1;
}

.sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

div.sliderbutton {
    position: absolute;
    top: 80px;
    left: 0;
    width: 200px;
    height: 100px;
    border: 3px solid #73AD21;
}

div.drawing {
	position: absolute;
	top:0px;
	left:20;
}

img.hotbutton {
	cursor: pointer;
}

#drawing_canvas {

    border: 1px solid black;
}

/* The snackbar - position it at the bottom and in the middle of the screen */
#snackbar {
  visibility: hidden; /* Hidden by default. Visible on click */
  min-width: 250px; /* Set a default minimum width */
  margin-left: -125px; /* Divide value of min-width by 2 */
  background-color: #333; /* Black background color */
  color: #fff; /* White text color */
  text-align: center; /* Centered text */
  border-radius: 2px; /* Rounded borders */
  padding: 16px; /* Padding */
  position: fixed; /* Sit on top of the screen */
  z-index: 1; /* Add a z-index if needed */
  left: 50%; /* Center the snackbar */
  bottom: 30px; /* 30px from the bottom */
}

/* Show the snackbar when clicking on a button (class added with JavaScript) */
#snackbar.show {
  visibility: visible; /* Show the snackbar */
  /* Add animation: Take 0.5 seconds to fade in and out the snackbar. 
  However, delay the fade out process for 2.5 seconds */
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

/* Animations to fade the snackbar in and out */
@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;} 
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;} 
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}


.noteBar {
    height: 100%;
    width: 120;
    left: 600;
    top: 10;
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #999999;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;

}

</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<script src="js/include.js"></script>
<script>
//set up initial values -- clearly these are terrible
designMode = "brush"
mobile = false;
drawing = false;
tempArray = [];
tempmsg =  {};
brushSize = 2;
strokeColor = 0x000000;
thealpha = 1;
toolToggle = "close"
layerCount = 3;
focus = "all";
currentNoteHeight = 10;

function setMode(mode) {
	
	designMode = mode;

}



function mouseDown(e) {
	e.preventDefault();
	console.log("mouseDown()")
	
	if (toolToggle == "open") {
			toggleNav()
	}
	
	var mouseX = e.pageX - this.offsetLeft;
	var mouseY = e.pageY - this.offsetTop;
	
	if (focus == "all") {
		if (designMode == "brush") {
			//move to the correct starting position for drawing
			graphics.beginPath()
			graphics.strokeStyle = strokeColor;
			graphics.lineWidth = 10
			graphics.moveTo(mouseX, mouseY);
		
		
			drawing = true;
		}
	}
}

function mouseUp(e) {
	e.preventDefault();
	if (drawing) {
		drawing = false;
		console.log("mouseUp fired");
	
		brushoutput = JSON.stringify(tempArray);
		tempmsg = {
			user_id: "<?=$user_id;?>",
			project_id: <?=$project_id?>,
			type: "draw",
			draw: brushoutput
		
		
		
		
		}
	
		sendData(tempmsg);
	
		tempArray = [];
	
	
			//make a new canvas to draw
		var div = document.getElementById("grid");
		var canvasTmp = document.createElement("canvas");
		
		canvasTmp.id = "drawing_canvas" + layerCount;
		canvasTmp.style.left = "120px";
		canvasTmp.style.top = "0px";
		canvasTmp.width = 1200;
		canvasTmp.height = 900;
		canvasTmp.style.position = "absolute";
		canvasTmp.style.zIndex = layerCount;
		layerCount++;    

		graphics =canvasTmp.getContext("2d");

		div.appendChild(canvasTmp);
	
		canvasTmp.addEventListener("mousedown", mouseDown, false);
		canvasTmp.addEventListener("mouseup", mouseUp, false);
		canvasTmp.addEventListener("mousemove", draw, false);
	
		//make mobile work too https://developer.mozilla.org/en-US/docs/Web/API/Touch_events/Using_Touch_Events
		canvasTmp.addEventListener('touchstart', mouseDown, false);
		canvasTmp.addEventListener('touchend', mouseUp, false);
		canvasTmp.addEventListener('touchmove', draw, false);
	
	}
	
	
	
	
}


async function draw(e) { //should this be async? https://stackoverflow.com/questions/21518381/proper-way-to-wait-for-one-function-to-finish-before-continuing
	e.preventDefault();
	if (mobile) { 
	var mouseX = e.targetTouches[0].pageX - this.offsetLeft;
	var mouseY = e.targetTouches[0].pageY - this.offsetTop;
	} else {
	var mouseX = e.pageX - this.offsetLeft;
	var mouseY = e.pageY - this.offsetTop;
	
	}
	

	
	
	
	if (designMode == "brush") {
		if (drawing) {
			//graphics.lineStyle(brushSize,brushColor,thealpha); 
			graphics.lineWidth = brushSize;
			graphics.strokeStyle = strokeColor;
			graphics.lineTo(mouseX, mouseY);
			graphics.stroke();
			tempArray.push([{'type':'brush', 'color':graphics.strokeStyle, 'size':graphics.lineWidth, 'x':mouseX, 'y':mouseY}])
			//console.log(tempArray.slice(-1)[0])
			//var strokeArray = ["brush", brushSize, brushColor, mouseX, mouseY]
			//dataArray.push(strokeArray);
		}
	}
}

//initiate web socket
var webSocket = new WebSocket("ws://www.digitalwhimsylab.com:8080?project_id=<?=$project_id?>");


webSocket.onmessage = async function (event) {
 
 
 //make a new canvas to draw
	var div = document.getElementById("grid");
	var canvasTmp = document.createElement("canvas");
        
	canvasTmp.id = "drawing_canvas" + layerCount;
	canvasTmp.style.left = "120px";
	canvasTmp.style.top = "0px";
	canvasTmp.width = 1200;
	canvasTmp.height = 900;
	canvasTmp.style.position = "absolute";
	canvasTmp.style.zIndex = layerCount;
	layerCount++;    

	vgraphics =canvasTmp.getContext("2d");

	div.appendChild(canvasTmp);
	
	canvasTmp.addEventListener("mousedown", mouseDown, false);
	canvasTmp.addEventListener("mouseup", mouseUp, false);
	canvasTmp.addEventListener("mousemove", draw, false);
	
	//make mobile work too https://developer.mozilla.org/en-US/docs/Web/API/Touch_events/Using_Touch_Events
	canvasTmp.addEventListener('touchstart', mouseDown, false);
	canvasTmp.addEventListener('touchend', mouseUp, false);
	canvasTmp.addEventListener('touchmove', draw, false);
 
 
 
 
 
 
 // await draw();
  msg= JSON.parse(event.data);
  id = msg['id']
  
   if (msg['type'] == 'message') {
   console.log(msg['content'])
   
   snackBar(msg['content'])
   
   }
   
   if (msg['type'] == 'note') {
   
   tempDraw = msg['draw']
  	draws = JSON.parse(tempDraw)
   
   	console.log(JSON.stringify(msg))
   	//console.log("NOTE from "+ msg["user_id"] + ": " + decodeURIComponent(draws[0][0]["content"]))
   makeNote(msg["user_id"], decodeURIComponent(draws[0][0]["content"]))
   }
  
  
  if (msg['type'] == 'draw') {
  
  
  
  
  
  tempDraw = msg['draw']
  
  draws = JSON.parse(tempDraw)
  //console.log(tempDraw)
  //console.log(draws[0][0]['x'])
	draws.forEach(function(item,i){
		
		if (item[0]['type']=='brush'){
			if(i==0){
				vgraphics.beginPath()
				vgraphics.strokeStyle = item[0]['color'];;
				vgraphics.lineWidth = item[0]['size'];
				vgraphics.moveTo(item[0]['x'],item[0]['y']);
			}
			vgraphics.strokeStyle = item[0]['color'];;
			vgraphics.lineWidth = item[0]['size'];
			vgraphics.lineTo(item[0]['x'], item[0]['y']);
			vgraphics.stroke();
			
		}
	});
  }
}

function setSize(width) {
	
brushSize = width;	
console.log("brush :" + width);	
}

function setColor(whichColor){
	strokeColor = whichColor;
	console.log("color: " + strokeColor)
}


function sendData(data) {

	if(data["type"] == "message") {
		console.log(data["content"])
		webSocket.send(JSON.stringify(data));
	}
	
	if(data["type"] == "note") {
		console.log(data["content"])
		webSocket.send(JSON.stringify(data));
	}
	
	
	if(data["type"] == "draw" && data["draw"] != "[]") {
		webSocket.send(JSON.stringify(data));
		callAJAX('services/datahandler.php', 'mode=add&rawdata='+JSON.stringify(data), "consoleOutput")
	}
}


function consoleOutput(rmsg) {
console.log(decodeURIComponent(rmsg))
}


function drawLoadedData(rmsg) { //put the loaded data onto a canvas
	//console.log(decodeURIComponent(rmsg))
	var tempData = decodeURIComponent(rmsg)
	console.log(decodeURIComponent(tempData))
	jsondata = JSON.parse(decodeURIComponent(tempData));
	
	//create a base canvas to draw upon
	var div = document.getElementById("grid");
	
	var canvas = document.createElement("canvas");
        canvas.id = "base_canvas";
        canvas.style.left = "120px";
        canvas.style.top = "0px";
        canvas.width = 1200;
        canvas.height = 900;
        canvas.style.position = "absolute";
        canvas.style.zIndex = 1;    
	
	var graphics =canvas.getContext("2d");
	
		div.appendChild(canvas);
	
	//forEach jsondata['data'][0]['draw']
	
	
	jsondata['data'].forEach(function(el,i){
		el['draw'].forEach(function(item,i){
			if (item[0]['type']=='brush'){
				if(i==0){
					graphics.beginPath()
					graphics.strokeStyle = item[0]['color'];;
					graphics.lineWidth = item[0]['size'];
					graphics.moveTo(item[0]['x'],item[0]['y']);
				}
				graphics.strokeStyle = item[0]['color'];;
				graphics.lineWidth = item[0]['size'];
				graphics.lineTo(item[0]['x'], item[0]['y']);
				graphics.stroke();
				
			} else if (item[0]['type']=='note'){
				makeNote( el["id"] , decodeURIComponent(item[0]['content']))

			}
		
		})
	
	})
	







	canvas.addEventListener("mousedown", mouseDown, false);
	canvas.addEventListener("mouseup", mouseUp, false);
	canvas.addEventListener("mousemove", draw, false);
	
	//make mobile work too https://developer.mozilla.org/en-US/docs/Web/API/Touch_events/Using_Touch_Events
	canvas.addEventListener('touchstart', mouseDown, false);
	canvas.addEventListener('touchend', mouseUp, false);
	canvas.addEventListener('touchmove', draw, false);
	
	tempmsg = {
		user_id: "<?=$user_id;?>",
		project_id: <?=$project_id?>,
		type: "message",
		content: "New designer, <?= $user_id; ?> joined!" 
		
		
		
		
	}
	
	sendData(tempmsg);
	

}


window.onbeforeunload = function(){
 	tempmsg = {
		user_id: "<?=$user_id;?>",
		project_id: <?=$project_id?>,
		type: "message",
		content: "Designer <?= $user_id; ?> left..." 
		
		
		
		
	}
	
	sendData(tempmsg);
 
 
 
}


function loadData() {
callAJAX('services/datahandler.php', 'mode=retrieve&project_id='+<?=$project_id?>, "drawLoadedData")



}

function addNote() {
	focus = "here";
	setMode("note");
	
	noteDiv = document.createElement("div");
	noteDiv.style.padding = 10;
	noteDiv.align = "right"
	noteDiv.style.height = 275;
	noteDiv.style.width = 550;
	noteDiv.style.left = 150;
	noteDiv.style.top = 200;
	noteDiv.id = "notebox";
	noteDiv.style.position = "absolute";
	noteDiv.style.backgroundColor = '#ffcccc';
	noteDiv.style.zIndex = 1000;
	
	noteTitle = document.createTextNode("Note");
	noteText = document.createElement("textarea");
	noteText.id = "noteText";
	noteButton = document.createElement("button");
	noteCancel = document.createElement("button");
	
	noteCancel.textContent = "Cancel";
	noteButton.textContent = "Post";
	noteCancel.setAttribute('class', 'btn btn-danger');
	noteButton.setAttribute('class', 'btn btn-primary');
	
	
	
	
	noteText.name = "post";
	noteText.cols = "80";
	noteText.rows = "10";
	
	noteDiv.appendChild(noteTitle);
	noteDiv.appendChild(noteText);
	noteDiv.appendChild(noteCancel);
	noteDiv.appendChild(noteButton);
	
	
	var div = document.getElementById("grid");
	
	div.appendChild(noteDiv);
	noteCancel.addEventListener("click", cancelNote, false);
	noteButton.addEventListener("click", postNote, false);

}

function cancelNote() {
	focus = "all"
	console.log("close note")
	var div = document.getElementById("notebox");
	div.remove()
	setMode("brush");


}


function postNote() {

	var div = document.getElementById("notebox");
	var textBox = document.getElementById("noteText");

	textoutput = '[[{"type":"note","content":"'+ encodeURIComponent(textBox.value)    +'"}]]'

	data = {
				user_id: "<?=$user_id;?>",
				project_id: <?=$project_id?>,
				type: "note",
				draw: textoutput

			}

	sendData(data)
	console.log(JSON.stringify(data))
	callAJAX('services/datahandler.php', 'mode=add&rawdata='+encodeURIComponent(JSON.stringify(data)), "consoleOutput")


	console.log(textBox.value)
	cancelNote();
	makeNote("<?=$user_id;?>", textBox.value)

}


function showNotes() {




}


function makeNote(user, text) {

	noteDiv = document.createElement("div");
	noteDiv.style.padding = 10;
	
	//noteDiv.style.height = 25;
	noteDiv.style.width = 300;
	noteDiv.style.left = 975;
	noteDiv.style.top = currentNoteHeight;
	
	
	noteDiv.id = "notebox"+currentNoteHeight;
	noteDiv.style.position = "absolute";
	noteDiv.style.backgroundColor = randomColor();
	noteDiv.style.zIndex = 1000+currentNoteHeight;

	
	noteTitle = document.createElement("p");
	
	noteTitle.innerHTML = '<b>' + user + '</b> wrote "'+ text.replace(/\+/g,' ') +  '"'; 
	
	noteDiv.appendChild(noteTitle);
	
	noteDiv.style.animation = "fadein 0.5s";
	
	
  
	
	
	var div = document.getElementById("grid");
	div.appendChild(noteDiv);
	
	
	
	
	
	
	
	
	currentNoteHeight = currentNoteHeight + 55;


}


function randomColor() {

var colors = ['#acddde', '#e1f8dc', '#fef8dd', '#caf1de', '#ffcc33'];

return(colors[Math.floor(Math.random() * colors.length)]);





}


</script>


</head>
<body>
<div id="grid"></div>


<script>
	
	loadData();
	
	
	var div = document.getElementById("grid");
	
	
	
	
	var canvas = document.createElement("canvas");
        canvas.id = "drawing_canvas";
        canvas.style.left = "120px";
        canvas.style.top = "0px";
        canvas.width = 1200;
        canvas.height = 900;
        canvas.style.position = "absolute";
        canvas.style.zIndex = 2;    
	
		graphics =canvas.getContext("2d");
	
		div.appendChild(canvas);
	
	




	canvas.addEventListener("mousedown", mouseDown, false);
	canvas.addEventListener("mouseup", mouseUp, false);
	canvas.addEventListener("mousemove", draw, false);
	
	//make mobile work too https://developer.mozilla.org/en-US/docs/Web/API/Touch_events/Using_Touch_Events
	canvas.addEventListener('touchstart', mouseDown, false);
	canvas.addEventListener('touchend', mouseUp, false);
	canvas.addEventListener('touchmove', draw, false);


</script>

<div id="mySidenav" class="sidenav" onmouseOver="toggleNav()">

  <img src='img/tools/ink_blue.png' onmouseup='setColor("#0000ff")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/ink_red.png' onmouseup='setColor("#ff0000")' width = "50%" style="hotbutton"><br>
    <img src='img/tools/ink_black.png' onmouseup='setColor("#000000")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/ink_white.png' onmouseup='setColor("#ffffff")' width = "50%" style="hotbutton"><br>
  
  <img src='img/tools/ink_green.png' onmouseup='setColor("#00ff00")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/ink_yellow.png' onmouseup='setColor("#ffff00")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/ink_purple.png' onmouseup='setColor("#ff00ff")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/ink_orange.png' onmouseup='setColor("#ff9900")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/paint_up.png' onmouseup='setMode("brush")' width = "50%" style="hotbutton"><br>
  <img src='img/tools/note_up.png' onmouseup='addNote()' width = "50%" style="hotbutton"><br>
  
  
  <p>
 <img src='img/tools/line_small.png' onmouseup='setSize("2")' style="hotbutton"><img src='img/tools/line_med.png' onmouseup='setSize("10")' style="hotbutton"><img src='img/tools/line_big.png' onmouseup='setSize("20")' style="hotbutton"><br>
</div>


<div id="snackbar">message</div>  
<script>
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "20";
}

function toggleNav() {
	/*if (document.getElementById("mySidenav").style.width == "250px") {
		console.log("close it up")
		document.getElementById("mySidenav").style.width = "20";
		toolToggle = "close"
	} else {
		console.log("open it up" + document.getElementById("mySidenav").style.width)
		document.getElementById("mySidenav").style.width = "250";
		toolToggle = "open"
	}
	*/
	
}

function snackBar(message) { //https://www.w3schools.com/howto/howto_js_snackbar.asp
  // Get the snackbar DIV
  var x = document.getElementById("snackbar");
	x.innerHTML = message
  // Add the "show" class to DIV
  x.className = "show";

  // After 3 seconds, remove the show class from DIV
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}



</script>

   

</body>

<?php 

?>

<script>



</script>

</html>



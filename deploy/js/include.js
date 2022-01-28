//generic AJAX call 

function callAJAX(url, params, functionName) {
	
	console.log("callAJAX: url:" + url + ", params: " + params + ", functionName" + functionName)
	
	
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     //do something...call the function?
	 eval(functionName+"('" + xhttp.responseText + "')"); //must send back urlencoded
	 
    }
  };
  xhttp.open("POST", url, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
 // xhttp.setRequestHeader("Content-length", params.length);
 // xhttp.setRequestHeader("Connection", "close");
  xhttp.send(params);

	
}
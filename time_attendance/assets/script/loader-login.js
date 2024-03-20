var myVar;

function myFunction() {
  myVar = setTimeout(showPage, 2000);
}

function showPage() {
  document.getElementById("loader").style.display = "none";
  document.getElementById("Content").style.display = "block";
}

// Make sure to call myFunction to initiate the timeout.
myFunction();
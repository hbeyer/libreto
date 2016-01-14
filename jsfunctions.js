function toggle(control){
	var elem = document.getElementById(control);
	
	if(elem.style.display == "none"){
		elem.style.display = "block";
		minus(control);
	}else{
		elem.style.display = "none";
		plus(control);
	}
}

function minus(control) {
    document.getElementById("link" + control).innerHTML = "Reduzieren";
}
function plus(control) {
    document.getElementById("link" + control).innerHTML = "Mehr";
}

function switchToOriginal() {
	var x = document.getElementsByClassName("titleBib");
	var y = document.getElementsByClassName("titleOriginal");
	var a = document.getElementsByClassName("authorName");
	var p = document.getElementsByClassName("published");
	var c = document.getElementsByClassName("titleOriginal-single");
	var countA = a.length;
	for (i = 0; i < countA; i++) {
		a[i].style.display='none';
		}
	var countP = p.length;
	for (i = 0; i < countP; i++) {
		p[i].style.display='none';
		}		
	var count = x.length;
	for (i = 0; i < count; i++) {
		x[i].style.display='none';
		y[i].style.display='inline';
		}
	var countC = c.length;
	for (i = 0; i < countC; i++) {
		c[i].style.display='none';
		}			
	z = document.getElementById("switchLink");
	z.innerHTML = "<a href='javascript:switchToBibl()'>Bibliographierte Daten anzeigen</a>";
	/* z = document.getElementById('button');
	z.innerHTML = "<button class='btn btn-default' onclick='switchToBibl()'>Bibliographische Titel</button>"; */
	}

function switchToBibl() {
	var x = document.getElementsByClassName("titleBib");
	var y = document.getElementsByClassName("titleOriginal");
	var a = document.getElementsByClassName("authorName");
	var p = document.getElementsByClassName("published");
	var c = document.getElementsByClassName("titleOriginal-single");
	var countA = a.length;
	for (i = 0; i < countA; i++) {
		a[i].style.display='inline';
		}
	var countP = p.length;
	for (i = 0; i < countP; i++) {
		p[i].style.display='inline';
		}		
	var count = x.length;
	for (i = 0; i < count; i++) {
		y[i].style.display='none';
		x[i].style.display='inline';
		}
	var countC = c.length;
	for (i = 0; i < countC; i++) {
		c[i].style.display='inline';
		}			
	z = document.getElementById("switchLink");
	z.innerHTML = "<a href='javascript:switchToOriginal()'>Transkription des Katalogs</a>";
	/* z = document.getElementById('button');
	z.innerHTML = "<button class='btn btn-default' onclick='switchToOriginal()'>Originaltitel</button>"; */
	}

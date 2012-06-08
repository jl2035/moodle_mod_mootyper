var startTime;
var endTime;
var napake;
var trenutnaPos;
var started = false;
var ended = false;
var trenutniChar;
var fullText;
var intervalID = -1;

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}

function moveCursor(nextPos)
{
	if(nextPos > 0 && nextPos <= fullText.length){
		document.getElementById('crka'+(nextPos-1)).className = "txtZeleno";
		}
	if(nextPos < fullText.length)
		document.getElementById('crka'+(nextPos)).className = "txtModro";
}

function doKonec()
{
	document.getElementById('crka'+(fullText.length-1)).className = "txtZeleno";
	ended = true;
	clearInterval(intervalID);
	endTime = new Date();
	razlikaT = timeRazlika(startTime, endTime);
	var hours = razlikaT.getHours();
	var mins = razlikaT.getMinutes();
	var secs = razlikaT.getSeconds();
	var samoSekunde = dobiSekunde(hours, mins, secs); 
	var reportJ = "Napake: "+napake+"<br>Cas: "+samoSekunde+" s";
	reportJ += "<br>Udarci: "+(fullText.length + napake);
	reportJ += "<br>Točnost: "+ izracunajTocnost(fullText, napake).toFixed(2)+"%";   
	reportJ += "<br>Udarci/minuto: "+izracunajHitrost(samoSekunde).toFixed(2); 
	document.getElementById('rdDiv2').innerHTML = reportJ;
	document.form1.tb1.disabled="disabled";	
	document.form1.btnContinue.style.visibility="visible";
}

function keyboardElement(ltr)
{
	this.chr = ltr.toLowerCase();
	if(isLetter(ltr))
		this.shift = ltr.toUpperCase() == ltr;
	else if(ltr == 'Đ')
		this.shift = true;
	else
	{
		if(ltr == '!' || ltr == '"' || ltr == '#' || ltr == '$' || ltr == '%' || ltr == '&' ||
		   ltr == '/' || ltr == '(' || ltr == ')' || ltr == '=' || ltr == '?' || ltr == '*' || 
		   ltr == ':' || ltr == ';' || ltr == '>' || ltr == '_')
		    this.shift = true;
		else
			this.shift = false;
	}
	this.turnOn = function () { 
        if(isLetter(this.chr))
			document.getElementById(dobiTipkoId(this.chr)).className = "next"+dobiFinger(this.chr.toLowerCase());
		else if(this.chr == ' ')
			document.getElementById(dobiTipkoId(this.chr)).className = "nextSpace";
		else
			document.getElementById(dobiTipkoId(this.chr)).className = "next"+dobiFinger(this.chr.toLowerCase());
		if(this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r')
			document.getElementById('jkeyenter').classname = "next4";
		if(this.shift)
		{
			document.getElementById('jkeyshiftd').className="next4";
			document.getElementById('jkeyshiftl').className="next4";
		}
    }
    this.turnOff = function () {
		if(isLetter(this.chr))
        {
			if(this.chr == 'a' || this.chr == 's' || this.chr == 'd' || this.chr == 'f' ||
			   this.chr == 'j' || this.chr == 'k' || this.chr == 'l' || this.chr == 'č')
			   document.getElementById(dobiTipkoId(this.chr)).className = "finger"+dobiFinger(this.chr.toLowerCase());
			else 
				document.getElementById(dobiTipkoId(this.chr)).className = "normal";
		}
		else
			document.getElementById(dobiTipkoId(this.chr)).className = "normal";
		if(this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r')
			document.getElementById('jkeyenter').classname = "normal";			
		if(this.shift)
		{
			document.getElementById('jkeyshiftd').className="normal";
			document.getElementById('jkeyshiftl').className="normal";
		}
	}
}

function getPressedChar(e)
{
	var keynum
	var keychar
	var numcheck
	if(window.event) // IE
	{
	    keynum = e.keyCode
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
	    keynum = e.which
	}
	if(keynum == 13)
		keychar = '\n';
	else
		keychar = String.fromCharCode(keynum);
	return keychar;
}

function gumbPritisnjen(e)
{
	if(ended)
		return false;
	if(!started){
	    startTime = new Date();
	    napake = 0;
	    trenutnaPos = 0;
	    started = true;
	    trenutniChar = fullText[trenutnaPos];
	    intervalID = setInterval('updTimeSpeed()', 1000);
	}
	var keychar = getPressedChar(e);
	if(keychar == trenutniChar)
	{
		if(trenutnaPos == fullText.length-1)    //KONEC
	    {   
			doKonec();
			return true;
	    }
	    var thisE = new keyboardElement(keychar);
		thisE.turnOff();
		if(trenutnaPos < fullText.length-1){
			var nextChar = fullText[trenutnaPos+1];
			var nextE = new keyboardElement(nextChar);
			nextE.turnOn();
		}
		moveCursor(trenutnaPos+1);
		trenutniChar = fullText[trenutnaPos+1];
		trenutnaPos++;
	    return true;	
	}
	else if(keychar == ' ')
		return false;
	else
	{
		napake++;
		return false;
	}
}

function dobiSekunde(hrs, mins, seccs)
{
	if(hrs > 0)
		mins = (hrs*60) + mins;
	if(mins == 0)
		return seccs;
	else
		return (mins * 60) + seccs;
}

function timeRazlika(t1, t2)
{
	var yrs = t1.getFullYear();
	var mnth = t1.getMonth();
	var dys = t1.getDate();
	var h1 = t1.getHours();
	var m1 = t1.getMinutes();
	var s1 = t1.getSeconds();
	var h2 = t2.getHours();
	var m2 = t2.getMinutes();
	var s2 = t2.getSeconds();
	var ure = h2 - h1;
	var minute = m2 - m1;
	var secunde = s2 - s1;
	return new Date(yrs, mnth, dys, ure, minute, secunde, 0);
}

function initTextToEnter(ttext)
{
	var tempStr="";
	for(var i=0; i<ttext.length; i++)
	{
		var tChar = ttext[i];

		if(i==0)
			tempStr += "<span id='crka"+i+"' class='txtModro'>"+tChar+"</span>";
		else if(tChar == '\n')
			tempStr += "<span id='crka"+i+"' class='txtRdece'>&darr;</span><br>";
		else
		    tempStr += "<span id='crka"+i+"' class='txtRdece'>"+tChar+"</span>";
	}
	document.getElementById('textToEnter').innerHTML = tempStr;
	fullText = ttext;
	//prestaviCrke(0, ttext.length);
	//var tipkaID = dobiTipkoId(ttext[0].toLowerCase());
	//var fingerID = dobiFinger(ttext[0].toLowerCase());
	//document.getElementById(tipkaID).className = "next"+fingerID;
	//document.form1.hdnext.value = ttext[0];
}

function prestaviCrke(tPos, dolzina)
{
	document.getElementById('crka'+tPos).className = "txtZeleno";
	//alert("djla crka+"+tPos);
	if(dolzina-1 > tPos)
		document.getElementById('crka'+(tPos+1)).className = "txtModro";
	var crkaTmp1 = fullText.toLowerCase().charAt(tPos);
	var tipkaName = dobiTipkoId(crkaTmp1);
	if(tipkaName == "jkeya" || tipkaName == "jkeyč")
		document.getElementById(tipkaName).className = "finger4";
	else if(tipkaName == "jkeys" || tipkaName == "jkeyl")
		document.getElementById(tipkaName).className = "finger3";
	else if(tipkaName == "jkeyd" || tipkaName == "jkeyk")
		document.getElementById(tipkaName).className = "finger2";
	else if(tipkaName == "jkeyf" || tipkaName == "jkeyj")
		document.getElementById(tipkaName).className = "finger1";
	else if(tipkaName == "jkeyspace")
		document.getElementById(tipkaName).className = "normal";
	else
		document.getElementById(tipkaName).className = "normal";
	
	crkaTmp1 = fullText.toLowerCase().charAt(tPos+1);
	
	var fingerNext = dobiFinger(crkaTmp1);
	
	if(fingerNext == 5){
		document.getElementById(dobiTipkoId(crkaTmp1)).className = "nextSpace";
	alert("se zgodi");}
	else if(fingerNext == 6)
	{
		if(crkaTmp1 == ',')
			document.getElementById('jkeyvejica').className = "next2";
		else if(crkaTmp1 == '\n')
			document.getElementById('jkeyenter').className = "next4";
		else if(crkaTmp1 == '.')
			document.getElementById('jkeypika').className = "next3";
		else if(crkaTmp1 == '-')
		    document.getElementById('jkeypomisljaj').className = "next4";
	}
	else
		document.getElementById(dobiTipkoId(crkaTmp1)).className = "next"+fingerNext;
	
}

function dobiTipkoId(t_crka)
{
	if(t_crka == ' ')
		return "jkeyspace";
	else if(t_crka == ',' || t_crka == ';')
		return "jkeyvejica";
	else if(t_crka == '\n')
		return "jkeyenter";
	else if(t_crka == '.' || t_crka == ':')
		return "jkeypika";
	else if(t_crka == '-' || t_crka == '_')
		return "jkeypomislaj";
	else if(t_crka == '!')
		return "jkey1";
	else if(t_crka == '"')
		return "jkey2";
	else if(t_crka == '#')
		return "jkey3";
	else if(t_crka == '$')
		return "jkey4";
	else if(t_crka == '%')
		return "jkey5";
	else if(t_crka == '&')
		return "jkey6";
	else if(t_crka == '/')
		return "jkey7";
	else if(t_crka == '(')
		return "jkey8";
	else if(t_crka == ')')
		return "jkey9";
	else if(t_crka == '=')
		return "jkey0";
	else if(t_crka == '?' || t_crka == '\'')
		return "jkeyvprasaj";
	else if(t_crka == '*' || t_crka == '+')
		return "jkeyplus";
	else
		return "jkey"+t_crka;
		
}

function isDigit(aChar)
{
   myCharCode = aChar.charCodeAt(0);
   if((myCharCode > 47) && (myCharCode <  58))
   {
      return true;
   } 
   return false;
}
  
function dobiFinger(t_crka)
{
	if(t_crka == ' ')
		return 5;
	else if(t_crka == 'q' || t_crka == 'a' || t_crka == 'z' || t_crka == 'p' || t_crka == 'č' || t_crka == 'ć' ||
			t_crka == 'š' || t_crka =='đ' || t_crka == 'ž' || t_crka == 'đ' || t_crka == 'y' || t_crka == '1' || 
			t_crka == '2' || t_crka == '\'' || t_crka == '+' || t_crka == '*' || t_crka == '?' || t_crka == '!' ||
			t_crka == '\n' || t_crka == '-' || t_crka == '_')
		return 4;
	else if(t_crka == 'w' || t_crka == 's' || t_crka == 'x' || t_crka == ':' || t_crka == 'l' || t_crka == 'o' || 
	        t_crka == '0' || t_crka == '3' || t_crka == '#' || t_crka == '=' || t_crka == '.')
		return 3;
	else if(t_crka == 'd' || t_crka == 'e' || t_crka == 'c' || t_crka == '4' || t_crka == 'k' || t_crka == 'i' || 
	        t_crka == '9' || t_crka == ',' || t_crka == '$' || t_crka == ')' || t_crka == ';')
		return 2;
	else if(t_crka == 'r' || t_crka == 't' || t_crka == 'f' || t_crka == 'v' || t_crka == 'b' || t_crka == 'g' || 
	        t_crka == '5' || t_crka == '6' || t_crka == '7' || t_crka == '8' || t_crka == 'j' || t_crka == 'h' || 
	        t_crka == 'n' || t_crka == 'm' || t_crka == 'u' || t_crka == 'z' || t_crka == '%' || t_crka == '&' ||
	        t_crka == '/' || t_crka == '(')
		return 1;
	else
		return 6;
}

function izracunajHitrost(sc)
{
	return (((trenutnaPos + napake) * 60) / sc);
}

function izracunajTocnost()
{
	if(trenutnaPos+napake == 0)
		return 0;
	return ((trenutnaPos * 100) / (trenutnaPos+napake));
	
}

function updTimeSpeed()
{
	noviCas = new Date();
	tRazlika = timeRazlika(startTime, noviCas);
	var secs = dobiSekunde(tRazlika.getHours(), tRazlika.getMinutes(), tRazlika.getSeconds());
	document.getElementById('jsTime').innerHTML = secs;
	document.getElementById('jsSpeed').innerHTML = izracunajHitrost(fullText, napake, secs).toFixed(2);
	document.getElementById('jsMistakes').innerHTML = napake;
	document.getElementById('jsProgress').innerHTML = trenutnaPos + "/" +fullText.length;
	document.getElementById('jsSpeed').innerHTML = izracunajHitrost(secs).toFixed(2);
	document.getElementById('jsAcc').innerHTML = izracunajTocnost(fullText, napake).toFixed(2);
}

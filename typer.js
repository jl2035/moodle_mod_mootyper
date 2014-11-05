var startTime;
var endTime;
var napake;
var trenutnaPos;
var started = false;
var ended = false;
var trenutniChar;
var fullText;
var intervalID = -1;
var interval2ID = -1;
var app_url;
var show_keyboard;
var THE_LAYOUT;

function moveCursor(nextPos) {
	if(nextPos > 0 && nextPos <= fullText.length) {
		$('#crka'+(nextPos-1)).addClass('txtZeleno');
		$('#crka'+(nextPos-1)).removeClass('txtModro');
		$('#crka'+(nextPos-1)).removeClass('txtRdece');
	}
	if(nextPos < fullText.length)
		$('#crka'+nextPos).addClass('txtModro');
}

function doKonec() {
	$('#crka'+(fullText.length-1)).addClass('txtZeleno');
	$('#crka'+(fullText.length-1)).removeClass('txtModro');
	$('#crka'+(fullText.length-1)).removeClass('txtRdece');
	ended = true;
	clearInterval(intervalID);
	clearInterval(interval2ID);
	endTime = new Date();
	razlikaT = timeRazlika(startTime, endTime);
	var hours = razlikaT.getHours();
	var mins = razlikaT.getMinutes();
	var secs = razlikaT.getSeconds();
	var samoSekunde = dobiSekunde(hours, mins, secs);
	$('input[name="rpFullHits"]').val((fullText.length + napake));
	$('input[name="rpTimeInput"]').val(samoSekunde);
	$('input[name="rpMistakesInput"]').val(napake);
	var speed = izracunajHitrost(samoSekunde);
	$('input[name="rpAccInput"]').val(izracunajTocnost(fullText, napake).toFixed(2));
	$('input[name="rpSpeedInput"]').val(speed);
	$('#tb1').attr('disabled', 'disabled');
	$('#btnContinue').css('visibility', 'visible');
	var wpm = (speed / 5) - napake;
	$('#jsWpm').html(wpm.toFixed(2));
    var juri =  app_url+"/mod/mootyper/atchk.php?status=3&attemptid="+$('input[name="rpAttId"]').val();
	$.get(juri, function( data ) { });
}

function getPressedChar(e) {
	var keynum;
	var keychar;
	var numcheck;
	if(window.event)
	    keynum = e.keyCode;
	else if(e.which)
	    keynum = e.which;
	if(keynum == 13)
		keychar = '\n';
	/*THIS HACK IS NEEDED FOR SPANISH KEYBOARD, WHICH USES 161 for some character*/
	//else if(!keynum || keynum == 160 || keynum == 161)
	else if((!keynum || keynum == 160 || keynum == 161) && (keynum != 161 && THE_LAYOUT!='Spanish'))
		keychar = '[not_yet_defined]';
	else
		keychar = String.fromCharCode(keynum);
	return keychar;
}

function focusSet(e) {
	if(!started) {
		$('#tb1').val('');
		if(show_keyboard){
			var thisEl = new keyboardElement(fullText[0]);
			thisEl.turnOn();
		}
		return true;
	}
	else{
		$('#tb1').val(fullText.substring(0, trenutnaPos));
		return true;
	}
}

function doCheck() {
    var rpMootyperId = $('input[name="rpSityperId"]').val();
    var rpUser = $('input[name="rpUser"]').val();
    var rpAttId = $('input[name="rpAttId"]').val();
    var juri =  app_url+"/mod/mootyper/atchk.php?status=2&attemptid="+rpAttId+"&mistakes="+napake+"&hits="+(trenutnaPos+napake);
	$.get(juri, function( data ) { });
}

function doStart() {
	startTime = new Date();
	napake = 0;
	trenutnaPos = 0;
	started = true;
	trenutniChar = fullText[trenutnaPos];
	intervalID = setInterval('updTimeSpeed()', 1000);
    var rpMootyperId = $('input[name="rpSityperId"]').val();
    var rpUser = $('input[name="rpUser"]').val();
    var juri =  app_url+"/mod/mootyper/atchk.php?status=1&mootyperid="+rpMootyperId+"&userid="+rpUser+"&time="+(startTime.getTime()/1000);
    $.get(juri, function( data ) { 
		$('input[name="rpAttId"]').val(data);
	});
	interval2ID = setInterval('doCheck()', 4000);
}

function gumbPritisnjen(e) {
	if(ended)
		return false;
	if(!started)
		doStart();
	var keychar = getPressedChar(e);
	if(keychar == trenutniChar || ((trenutniChar == '\n' || trenutniChar == '\r\n' || trenutniChar == '\n\r' || trenutniChar == '\r') && (keychar == ' ')))
	{
		if(trenutnaPos == fullText.length-1) {  //END
			$('#tb1').val($('#tb1').val()+trenutniChar);
			var elemOff = new keyboardElement(trenutniChar);
			elemOff.turnOff();
			doKonec();
			return true;
	    }
	    
	    if(trenutnaPos < fullText.length-1){
			var nextChar = fullText[trenutnaPos+1];
			if(show_keyboard){
				var thisE = new keyboardElement(trenutniChar);
				thisE.turnOff();
				if(isCombined(nextChar) && (thisE.shift || thisE.alt || thisE.pow || thisE.uppercase_umlaut))
					combinedCharWait = true;
				var nextE = new keyboardElement(nextChar);
				nextE.turnOn();
			}
			if(isCombined(nextChar)) {
				$("#form1").off("keypress", "#tb1", gumbPritisnjen);
				$("#form1").on("keyup", "#tb1", keyupFirst);
			}
		}
		moveCursor(trenutnaPos+1);
		trenutniChar = fullText[trenutnaPos+1];
		trenutnaPos++;
	    return true;	
	}
	else if(keychar == ' ')  //I don't remember why we're having this if
		return false;
	else {
		napake++;
		return false;
	}
}

function dobiSekunde(hrs, mins, seccs) {
	if(hrs > 0)
		mins = (hrs*60) + mins;
	if(mins == 0)
		return seccs;
	else
		return (mins * 60) + seccs;
}

function timeRazlika(t1, t2) {
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

function initTextToEnter(ttext, tinprogress, tmistakes, thits, tstarttime, tattemptid, turl, tshowkeyboard) {
	$("#form1").on("keypress", "#tb1", gumbPritisnjen);
	show_keyboard = tshowkeyboard;
	fullText = ttext;
	app_url = turl;
	var tempStr="";
	if(tinprogress) {
		$('input[name="rpAttId"]').val(tattemptid);
		startTime = new Date(tstarttime*1000);
		napake = tmistakes;
		trenutnaPos = (thits - tmistakes);   //!!!!!!!!!!!!!!!!!!!!!!!!!!!
	    trenutniChar = fullText[trenutnaPos];
	    if(show_keyboard) {
			var nextE = new keyboardElement(trenutniChar);
			nextE.turnOn();
			if(isCombined(trenutniChar)) {
				$("#form1").off("keypress", "#tb1", gumbPritisnjen);
				$("#form1").on("keyup", "#tb1", keyupCombined);
			}
		}
	    started = true;
	    intervalID = setInterval('updTimeSpeed()', 1000);
	    interval2ID = setInterval('doCheck()', 3000);
		for(var i=0; i<trenutnaPos; i++) {
			var tChar = ttext[i];
			if(tChar == '\n')
				tempStr += "<span id='crka"+i+"' class='txtZeleno'>&darr;</span><br>";
			else
				tempStr += "<span id='crka"+i+"' class='txtZeleno'>"+tChar+"</span>";
		}
		tempStr += "<span id='crka"+trenutnaPos+"' class='txtModro'>"+trenutniChar+"</span>";
		for(var j=trenutnaPos+1; j<ttext.length; j++) {
			var tChar = ttext[j];
			if(tChar == '\n')
				tempStr += "<span id='crka"+j+"' class='txtRdece'>&darr;</span><br>";
			else
				tempStr += "<span id='crka"+j+"' class='txtRdece'>"+tChar+"</span>";
		}
	}
	else
	{
		for(var i=0; i<ttext.length; i++)
		{
			var tChar = ttext[i];
			
			if(i==0) {
				tempStr += "<span id='crka"+i+"' class='txtModro'>"+tChar+"</span>";
				if(isCombined(tChar)) {
					$("#form1").off("keypress", "#tb1", gumbPritisnjen);
					$("#form1").on("keyup", "#tb1", keyupCombined);
				}
			}
			else if(tChar == '\n')
				tempStr += "<span id='crka"+i+"' class='txtRdece'>&darr;</span><br>";
			else
				tempStr += "<span id='crka"+i+"' class='txtRdece'>"+tChar+"</span>";
		}
	}
	$('#textToEnter').html(tempStr);
}

function isDigit(aChar) {
	myCharCode = aChar.charCodeAt(0);
	if((myCharCode > 47) && (myCharCode <  58))
		return true; 
	return false;
}

function izracunajHitrost(sc) {
	return (((trenutnaPos + napake) * 60) / sc);
}

function izracunajTocnost() {
	if(trenutnaPos+napake == 0)
		return 0;
	return ((trenutnaPos * 100) / (trenutnaPos+napake));
}

function updTimeSpeed() {	
	noviCas = new Date();
	tRazlika = timeRazlika(startTime, noviCas);
	var secs = dobiSekunde(tRazlika.getHours(), tRazlika.getMinutes(), tRazlika.getSeconds());
	$('#jsTime').html(secs);
	//$('#jsSpeed').html(izracunajHitrost(fullText, napake, secs).toFixed(2));
	$('#jsMistakes').html(napake);
	$('#jsProgress').html(trenutnaPos + "/" +fullText.length);
	$('#jsSpeed').html(izracunajHitrost(secs).toFixed(2));
	$('#jsAcc').html(izracunajTocnost(fullText, napake).toFixed(2));
}

var combinedChar = false;
var combinedCharWait = false;
THE_LAYOUT = 'GermanSwiss';
function keyboardElement(ltr) {
	this.chr = ltr.toLowerCase();
	this.alt = false;
	this.uppercase_umlaut = false;
	this.pow = false;
	if(isLetter(ltr))
		this.shift = ltr.toUpperCase() == ltr;
	else if(ltr == '@' || ltr == '€' || ltr == '#' || ltr == '|' || ltr == '½' || ltr == '¬' || ltr == '¢' || ltr == '\\')
	{
		this.shift = false;
		this.alt = true;
	}
	else
	{
		if(ltr == '°' || ltr == '!' || ltr == '+' || ltr == '"' || ltr == '*' || ltr == 'ç' || ltr == '%' || ltr == '&' || ltr == '/' || ltr == '(' || ltr == ')' || ltr == '=' || ltr == '?' || ltr == ':' || ltr == ';' || ltr == '>' || ltr == '_')
		    this.shift = true;
		else
			this.shift = false;
	}
	if(ltr == 'è' || ltr == 'é' || ltr == 'à')
		this.shift = true;
	if(ltr == 'â' || ltr == 'î' || ltr == 'ô' || ltr == 'ê')
		this.pow = true;
	if(ltr == 'Ô' || ltr == 'Â' || ltr == 'Ê' || ltr == 'Û') {
		this.shift = true;
		this.alt = false;
		this.pow = true;
	}
	if(ltr == 'Ö' || ltr == 'Ä' || ltr == 'Ü' || ltr == 'Ë') {
		this.uppercase_umlaut = true;
		this.shift = true;
		this.alt = false;
	}
	this.turnOn = function () {
		if(this.uppercase_umlaut) {
			var ukey = convertFromUpperUmlaut(this.chr.toUpperCase());
			document.getElementById('jkeygerklicaj').className = 'next4';
			document.getElementById('jkey'+ukey).className = 'next'+dobiFinger(ukey);
		}
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
		if(this.alt)
			document.getElementById('jkeyaltgr').className="nextSpace";
		if(this.pow)
			document.getElementById('jkeypow').className="nextSpace";
    };
    this.turnOff = function () {
		if(this.uppercase_umlaut) {
			var ukey = convertFromUpperUmlaut(this.chr.toUpperCase());
			document.getElementById('jkeygerklicaj').className = 'normal';
			document.getElementById('jkey'+ukey).className = 'normal';
		}
		else if(this.chr == 'a' || this.chr == 'â' || this.chr == 's' || this.chr == 'd' || this.chr == 'f' ||
			this.chr == 'j' || this.chr == 'k' || this.chr == 'l' || this.chr == 'ö' || this.chr == 'é')
			document.getElementById(dobiTipkoId(this.chr)).className = "finger"+dobiFinger(this.chr.toLowerCase());
		else 
			document.getElementById(dobiTipkoId(this.chr)).className = "normal";
		if(this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r')
			document.getElementById('jkeyenter').classname = "normal";			
		if(this.shift)
		{
			document.getElementById('jkeyshiftd').className="normal";
			document.getElementById('jkeyshiftl').className="normal";
		}
		if(this.alt)
			document.getElementById('jkeyaltgr').className="normal";
		if(this.pow)
			document.getElementById('jkeypow').className="normal";
	};
}

function isCombined(chr) {
	return (chr == 'â' || chr == 'î' || chr == 'ô' || chr == 'ê' || chr == 'Ü' || chr == 'Ä' || chr == 'Ö' || chr == 'Ë' || chr == 'Û' || chr == 'Â' || chr == 'Ô' || chr == 'Ê');
}

function keyupCombined(e) {
	if(ended)
		return false;
	if(!started)
		doStart();
	var keychar = getPressedChar(e);
	if(keychar == '[not_yet_defined]') {
		combinedChar = true;
		return true;
	}
	if(combinedCharWait) {
		combinedCharWait = false;
		return true;
	}
	var currentText = $('#tb1').val();
	var lastChar = currentText.substring(currentText.length-1);
	if(combinedChar && lastChar==currentChar) 
	// && ((currentChar.toUpperCase() == currentChar && e.shiftKey) || (currentChar.toUpperCase() != currentChar))) 
	{
		if(show_keyboard){
			var thisE = new keyboardElement(currentChar);
			thisE.turnOff();
		}
		if(currentPos == fullText.length-1) {   //END   
			doKonec();
			return true;
		}
		if(currentPos < fullText.length-1){
			var nextChar = fullText[currentPos+1];
			if(show_keyboard){
				var nextE = new keyboardElement(nextChar);
				nextE.turnOn();
			}
			if(!isCombined(nextChar)) {            //If next char is not combined char
				$("#form1").off("keyup", "#tb1");
				$("#form1").on("keypress", "#tb1", keyPressed);
			}
		}
		combinedChar = false;
		moveCursor(currentPos+1);
		currentChar = fullText[currentPos+1];
		currentPos++;
		return true;
	}
	else
	{
		combinedChar = false;
		napake++;
		var tbval = $('#tb1').val();
		$('#tb1').val(tbval.substring(0, currentPos));
		return false;
	}	
}

function keyupFirst(event) {
	$("#form1").off("keyup", "#tb1", keyupFirst);
	$("#form1").on("keyup", "#tb1", keyupCombined);
	return false;
}

function convertFromUpperUmlaut(c) {
	if(c == 'Ü' || c == 'Û')
		return 'u';
	else if(c == 'Ä' || c == 'Â')
		return 'a';
	else if(c == 'Ö' || c == 'Ô')
		return 'o';
	else if(c == 'Ë' || c == 'Ê')
		return 'e';
	else
		return null;
}

function dobiFinger(t_crka) {
	if(t_crka == ' ')
		return 5;
	else if(t_crka == 'q' || t_crka == 'a' || t_crka == 'â' || t_crka == 'p' || t_crka == 'ö' || t_crka == 'ä' || t_crka == 'ü' || t_crka == 'è' || t_crka == 'é' || t_crka == 'à' || t_crka == '$' || t_crka == '¨' || t_crka == 'y' || t_crka == '1' || t_crka == '2' || t_crka == '\'' || t_crka == '+' || t_crka == '?' || t_crka == '@' || t_crka == '\n' || t_crka == '-' || t_crka == '_' || t_crka == '<' || t_crka == '>' || t_crka == '!' || t_crka == '°' || t_crka == 'â')
		return 4;
	else if(t_crka == 'w' || t_crka == 's' || t_crka == 'x' || t_crka == ':' || t_crka == 'l' || t_crka == 'o' || t_crka == '0' || t_crka == '3' || t_crka == '#' || t_crka == '=' || t_crka == '.' || t_crka == '*' || t_crka == 'ô')
		return 3;
	else if(t_crka == 'd' || t_crka == 'e' || t_crka == 'c' || t_crka == '4' || t_crka == 'k' || t_crka == 'i' || t_crka == '9' || t_crka == ',' || t_crka == ')' || t_crka == ';' || t_crka == 'ç' || t_crka == 'î' || t_crka == 'ê')
		return 2;
	else if(t_crka == 'r' || t_crka == 't' || t_crka == 'f' || t_crka == 'v' || t_crka == 'b' || t_crka == 'g' || t_crka == '5' || t_crka == '6' || t_crka == '7' || t_crka == '8' || t_crka == 'j' || t_crka == 'h' || t_crka == 'n' || t_crka == 'm' || t_crka == 'u' || t_crka == 'z' || t_crka == '%' || t_crka == '&' || t_crka == '/' || t_crka == '(')
		return 1;
	else
		return 6;
}

function dobiTipkoId(t_crka) {
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
		return "jkeygerklicaj";
	else if(t_crka == '$')
		return 'jkeygerdollar';
	else if(t_crka == '"')
		return "jkey2";
	else if(t_crka == '*')
		return "jkey3";
	else if(t_crka == 'ç')
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
	else if(t_crka == '^')
		return "jkeypow";
	else if(t_crka == '<' || t_crka == '>')
		return "jkeyckck";
	else if(t_crka == '@')
		return "jkey2";
	else if(t_crka == 'è')
		return "jkeyü";
	else if(t_crka == 'é')
		return "jkeyö";
	else if(t_crka == 'à')
		return "jkeyä";
	else if(t_crka == '°')
		return "jkeytildo";
	else if(t_crka == 'î')
		return "jkeyi";
	else if(t_crka == 'â')
		return "jkeya";
	else if(t_crka == 'ô')
		return "jkeyo";
	else if(t_crka == 'ê')
		return "jkeye";
	else if(t_crka == 'Ü' || t_crka == 'Û')
		return "jkeyu";
	else if(t_crka == 'Ö' || t_crka == 'Ô')
		return "jkeyo";
	else if(t_crka == 'Ë' || t_crka == 'Ê')
		return "jkeye";
	else if(t_crka == 'Ä' || t_crka == 'Â')
		return "jkeya";
	else
		return "jkey"+t_crka;
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-zčšžđćüöäî]/i);
}

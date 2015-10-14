function isCombined(chr) {
	return false;
}

function keyupCombined(e) {
	return false;	
}

function keyupFirst(event) {
	return false;
}

function keyboardElement(ltr) {
	this.chr = ltr.toLowerCase();
	this.alt = false;
	if(isLetter(ltr))
		this.shift = ltr.toUpperCase() == ltr;
	else
	{
		if(ltr.match(/[!"№;%:?*()_+/,]/i))
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
			document.getElementById('jkeyenter').className = "next4";
		if(this.shift)
		{
			document.getElementById('jkeyshiftd').className="next4";
			document.getElementById('jkeyshiftl').className="next4";
		}
		if(this.alt)
			document.getElementById('jkeyaltgr').className="nextSpace";
	};
	this.turnOff = function () {
		if(isLetter(this.chr))
		{
			if(this.chr.match(/[фываолдж]/i))
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
		if(this.alt)
			document.getElementById('jkeyaltgr').className="normal";
	};
}

function dobiFinger(t_crka) {
	if(t_crka == ' ')
		return 5;
	else if(t_crka.match(/[ё1!йфя0\-=\\)_+/зхъжэ.,]/i))
		return 4;
	else if(t_crka.match(/[2цыч"9щдю(]/i))
		return 3;
	else if(t_crka.match(/[3увс№8шлб*]/i))
		return 2;
	else if(t_crka.match(/[4кам5епи;%6нрт7гоь:?]/i))
		return 1;
	else
		return 6;
}

function dobiTipkoId(t_crka) {
	if(t_crka == ' ')
		return "jkeyspace";
	else if(t_crka == '\n')
		return "jkeyenter";
	else if(t_crka == '!')
		return "jkey1";
	else if(t_crka == '"')
		return "jkey2";
	else if(t_crka == '№')
		return "jkey3";
	else if(t_crka == ';')
		return "jkey4";
	else if(t_crka == '%')
		return "jkey5";
	else if(t_crka == ':')
		return "jkey6";
	else if(t_crka == '?')
		return "jkey7";
	else if(t_crka == '*')
		return "jkey8";
	else if(t_crka == '(')
		return "jkey9";
	else if(t_crka == ')')
		return "jkey0";
	else if(t_crka ==  '-' || t_crka == '_')
		return "jkeypomislaj";
	else if(t_crka == '.' || t_crka == ',')
		return "jkeypika";
	else if(t_crka == '=' || t_crka == '+')
		return "jkeyequals";
	else if(t_crka == "\\" || t_crka == '/')
		return "jkeybackslash";
	else
		return "jkey"+t_crka;
}

function isLetter(str) {
	return str.length === 1 && str.match(/[ёйцукенгшщзхъфывапролджэячсмитьбю]/i);
}

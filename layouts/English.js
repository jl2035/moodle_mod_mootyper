function keyboardElement(ltr)
{
	this.chr = ltr.toLowerCase();
	this.alt = false;
	if(isLetter(ltr))
		this.shift = ltr.toUpperCase() == ltr;
	else
	{
		if(ltr == '!' || ltr == '@' || ltr == '#' || ltr == '$' || ltr == '%' || ltr == '^' ||
		   ltr == '&' || ltr == '(' || ltr == ')' || ltr == '*' || ltr == '_' || ltr == '+' || 
		   ltr == ':' || ltr == '"' || ltr == '|' || ltr == '{' || ltr == '}' || ltr == '>' || ltr == '?')
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
		if(this.alt)
			document.getElementById('jkeyaltgr').className="nextSpace";
    }
    this.turnOff = function () {
		if(isLetter(this.chr))
        {
			if(this.chr == 'a' || this.chr == 's' || this.chr == 'd' || this.chr == 'f' ||
			   this.chr == 'j' || this.chr == 'k' || this.chr == 'l' || this.chr == ';')
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
	}
}

function dobiFinger(t_crka)
{
	if(t_crka == ' ')
		return 5;
	else if(t_crka == 'q' || t_crka == 'a' t_crka == '1' ||  t_crka == '2' || t_crka == 'z' || t_crka == '<' || 
			t_crka == 'p' || t_crka == '{' || t_crka == '}' || t_crka == ';' || 
			t_crka == '\'' || t_crka == '\\' || t_crka == '/' || t_crka == '=' || t_crka == '-' ||
			t_crka == '!' || t_crka == '@' || t_crka == '>' || t_crka == '[' || t_crka == ']' ||
			t_crka == ':' || t_crka == '"' || t_crka == '|' || t_crka == '?' || t_crka == '+' || t_crka == '_')
		return 4;
	else if(t_crka == 'w' || t_crka == 's' || t_crka == 'x' || t_crka == '3' || t_crka == '#' || t_crka == '.' || 
	        t_crka == 'l' || t_crka == 'o' || t_crka == '0' || t_crka == ')')
		return 3;
	else if(t_crka == 'd' || t_crka == 'e' || t_crka == 'c' || t_crka == '4' || t_crka == 'k' || t_crka == 'i' || 
	        t_crka == '9' || t_crka == ',' || t_crka == '$' || t_crka == '(')
		return 2;
	else if(t_crka == 'v' || t_crka == 'b' || t_crka == 'f' || t_crka == 'g' || t_crka == 'r' || t_crka == 't' || 
	        t_crka == '5' || t_crka == '6' || t_crka == '7' || t_crka == '8' || t_crka == '%' || t_crka == '^' || 
	        t_crka == 'm' || t_crka == 'n' || t_crka == 'j' || t_crka == 'h' || t_crka == 'u' || t_crka == 'y' ||
	        t_crka == '*' || t_crka == '&')
		return 1;
	else
		return 6;
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
	else if(t_crka == '@')
		return "jkey2";
	else if(t_crka == '#')
		return "jkey3";
	else if(t_crka == '$')
		return "jkey4";
	else if(t_crka == '%')
		return "jkey5";
	else if(t_crka == '^')
		return "jkey6";
	else if(t_crka == '&')
		return "jkey7";
	else if(t_crka == '*')
		return "jkey8";
	else if(t_crka == '(')
		return "jkey9";
	else if(t_crka == ')')
		return "jkey0";
	else if(t_crka ==  '-' || t_crka == '_')	
		return "jkeypomislaj";
	else if(t_crka == '[' || t_crka == '{')	
		return "jkeyoglokl";
	else if(t_crka == ']' || t_crka == '}')
		return "jkeyoglzak";
	else if(t_crka == ';' || t_crka == ':')
		return "jkeypodpicje";
	else if(t_crka == "'" || t_crka == '"')
		return "jkeycrtica";
	else if(t_crka == "\\" || t_crka == '|')
		return "jkeybackslash";
	else if(t_crka == ',')
		return "jkeyvejica";
	else if(t_crka == '.')
		return "jkeypika";
	else if(t_crka == '=' || t_crka == '+')
		return "jkeyequals";
	else if(t_crka == '?' || t_crka == '/')
		return "jkeyslash";
	else if(t_crka == '<' || t_crka == '>')
		return "jkeyckck";
	else
		return "jkey"+t_crka;
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}

function isCombined(chr) {
  return false;
}

function keyupCombined(e) {
  return false; 
}

function keyupFirst(event) {
  return false;
}

THE_LAYOUT = 'Japanese';

function keyboardElement(ltr) {
  this.chr = ltr.toLowerCase();
  this.alt = false;
  if(isLetter(ltr))
    this.shift = ltr.toUpperCase() == ltr;
  else
  {
    if(ltr == '!' || ltr == '"' || ltr == '#' || ltr == '$' || ltr == '%' || ltr == '&' ||
       ltr == '\'' || ltr == '(' || ltr == ')' || ltr == '' || ltr == '=' || ltr == '~' || ltr == '|' || 
       ltr == '`' || ltr == '{' || ltr == '+' || ltr == '*' || ltr == '}' ||
       ltr == '<' || ltr == '>' || ltr == '?' || ltr == '_')
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
      document.getElementById('jkeyshiftr').className="next4";
      document.getElementById('jkeyshiftl').className="next4";
    }
    if(this.alt)
      document.getElementById('jkeyaltgr').className="nextSpace";
    };
    this.turnOff = function () {
    if(isLetter(this.chr))
        {
      if(this.chr == 'a' || this.chr == 's' || this.chr == 'd' || this.chr == 'f' ||
         this.chr == 'j' || this.chr == 'k' || this.chr == 'l')
         document.getElementById(dobiTipkoId(this.chr)).className = "finger"+dobiFinger(this.chr.toLowerCase());
      else 
        document.getElementById(dobiTipkoId(this.chr)).className = "normal";
    }
    else if(this.chr == ':' || this.chr == ';')             //English specific ; and :
      document.getElementById(dobiTipkoId(this.chr)).className = "finger4";
    else
      document.getElementById(dobiTipkoId(this.chr)).className = "normal";
    if(this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r')
      document.getElementById('jkeyenter').classname = "normal";      
    if(this.shift)
    {
      document.getElementById('jkeyshiftr').className="normal";
      document.getElementById('jkeyshiftl').className="normal";
    }
    if(this.alt)
      document.getElementById('jkeyaltgr').className="normal";
  };
}

function dobiFinger(t_crka) {
  if(t_crka == ' ')
    return 5;
  else if(t_crka == '1' || t_crka == 'q' || t_crka == 'a' || t_crka == 'z' ||
          t_crka == '!' ||
          t_crka == '0' || t_crka == 'p' || t_crka == ';' || t_crka == '/' ||
                                            t_crka == '+' || t_crka == '?' ||
          t_crka == '-' || t_crka == '@' || t_crka == ':' || t_crka == '\\' ||
          t_crka == '=' || t_crka == '`' || t_crka == '*' || t_crka == '_' ||
          t_crka == '^' || t_crka == '[' || t_crka == ']' ||
          t_crka == '~' || t_crka == '{' || t_crka == '}' ||
          t_crka == '\\' || 
          t_crka == '|')
    return 4;
  else if(t_crka == '2' || t_crka == 'w' || t_crka == 's' || t_crka == 'x' ||
          t_crka == '"' ||
          t_crka == '9' || t_crka == 'o' || t_crka == 'l' || t_crka == '.' ||
          t_crka == ')' ||                                   t_crka == '>')
    return 3;
  else if(t_crka == '3' || t_crka == 'e' || t_crka == 'd' || t_crka == 'c' ||
          t_crka == '#' ||
          t_crka == '8' || t_crka == 'i' || t_crka == 'k' || t_crka == ',' ||
          t_crka == '(' ||                                   t_crka == '<' )
    return 2;
  else if(t_crka == '4' || t_crka == 'r' || t_crka == 'f' || t_crka == 'v' ||
          t_crka == '$' ||
          t_crka == '5' || t_crka == 't' || t_crka == 'g' || t_crka == 'b' ||
          t_crka == '%' ||
          t_crka == '6' || t_crka == 'y' || t_crka == 'h' || t_crka == 'n' ||
          t_crka == '&' ||
          t_crka == '7' || t_crka == 'u' || t_crka == 'j' || t_crka == 'm' ||
          t_crka == '\'')
    return 1;
  else
    return 6;
}

function dobiTipkoId(t_crka) {
  if(t_crka == ' ')
    return "jkeyspace";
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
  else if(t_crka == '\'')
    return "jkey7";
  else if(t_crka == '(')
    return "jkey8";
  else if(t_crka == ')')
    return "jkey9";
  else if(t_crka == '-')
    return "jkeyminus";
  else if(t_crka == '^' || t_crka == '~')
    return "jkeycaret";
  else if(t_crka == '|')
    return "jkeyyen";
  else if(t_crka == '@' || t_crka == '`')
    return "jkeyat";
  else if(t_crka == '[' || t_crka == '{') 
    return "jkeybracketopen";
  else if(t_crka == ';' || t_crka == '+')
    return "jkeysemicolon";
  else if(t_crka == ':' || t_crka == '*')
    return "jkeypodpicje";
  else if(t_crka == ']' || t_crka == '}')
    return "jkeybracketclose";
  else if(t_crka == ',' || t_crka == '<')
    return "jkeycomma";
  else if(t_crka == '.' || t_crka == '>')
    return "jkeyperiod";
  else if(t_crka == '/' || t_crka == '?')
    return "jkeyslash";
  else if(t_crka == '\\' || t_crka == '_')
    return "jkeybackslash";
  else if(t_crka == '\n')
    return "jkeyenter";
  else
    return "jkey"+t_crka;
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}

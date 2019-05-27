//////////////////// Agenda file for CalendarXP 9.0 /////////////////
// This file is totally configurable. You may remove all the comments in this file to minimize the download size.
/////////////////////////////////////////////////////////////////////

//////////////////// Define agenda events ///////////////////////////
// Usage -- fAddEvent(year, month, day, message, action, bgcolor, fgcolor, bgimg, boxit, html);
// Notice:
// 1. The (year,month,day) identifies the date of the agenda.
// 2. In the action part you can use any javascript statement, or use " " for doing nothing.
// 3. Assign "null" value to action will result in a line-through effect(can't be selected).
// 4. html is the HTML string to be shown inside the agenda cell, usually an <img> tag.
// 5. fgcolor is the font color for the specific date. Setting it to ""(empty string) will make the fonts invisible and the date unselectable.
// 6. bgimg is the url of the background image file for the specific date.
// 7. boxit is a boolean that enables the box effect using the bgcolor when set to true.
// ** REMEMBER to enable respective flags of the gAgendaMask option in the theme, or it won't work.
/////////////////////////////////////////////////////////////////////

// fAddEvent(2003,12,2," Click me to active your email client. ","popup('mailto:any@email.address.org?subject=email subject')","#87ceeb","dodgerblue",null,true);
// fAddEvent(2004,4,1," Let's google. ","popup('http://www.google.com','_top')","#87ceeb","dodgerblue",null,true);
// fAddEvent(2004,9,23, "Hello World!\nYou can't select me.", null, "#87ceeb", "dodgerblue");




///////////// Dynamic holiday calculations /////////////////////////
// This function provides you a flexible way to make holidays of your own. (It's optional.)
// Once defined, it'll be called every time the calendar engine renders the date cell;
// With the date passed in, just do whatever you want to validate whether it is a desirable holiday;
// Finally you should return an agenda array like [message, action, bgcolor, fgcolor, bgimg, boxit, html] 
// to tell the engine how to render it. (returning null value will make it rendered as default style)
// ** REMEMBER to enable respective flags of the gAgendaMask option in the theme, or it won't work.
////////////////////////////////////////////////////////////////////
function fHoliday(y,m,d) {
	var rE=fGetEvent(y,m,d), r=null;

	// you may have sophisticated holiday calculation set here, following are only simple examples.
	if (m==1&&d==1)
		r=[" Confraternização Universal ",gsAction,"red","red"];
	else if (m==12&&d==25)
		r=[" Natal ",gsAction,"red","red"];
	else if (m==5&&d==1)
		r=[" Dia do Trabalho ",gsAction,"red","red"];
	else if (m==9&&d==7)
		r=[" Independência do Brasil",gsAction,"red","red"];
	else if (m==11&&d==15)
		r=[" Proclamação da República ",gsAction,"red","red"];
	else if (m==4&&d==10) 
		r=[" Paixão de Cristo ",gsAction,"red","red"];
	else if (m==4&&d==21) 
		r=[" Tiradentes ",gsAction,"red","red"];
	else if (m==5&&d==25) 
		r=[" Caravággio ",gsAction,"red","red"];
	else if (m==6&&d==11) 
		r=[" Corpus Christi ",gsAction,"red","red"];
	else if (m==9&&d==20) 
		r=[" Farroupilha ",gsAction,"red","red"];
	else if (m==10&&d==12) 
		r=[" Aparecida ",gsAction,"red","red"];		
	else if (m==11&&d==2) 
		r=[" Finados ",gsAction,"red","red"];		
	
	
	return rE?rE:r;	// favor events over holidays
}



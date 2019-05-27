<?
# PHP Interactive - an interactive shell for PHP
# Copyright (C) 2004 Elisa Manara
# Copyright (C) 2004 Salvatore Sanfilippo
# Check http://www.hping.org/phpinteractive for more information

error_reporting(E_ALL);

/* Similar to file_get_contents but will work with older PHP versions */
function comp_file_get_contents($filename) {
	return implode("", file($filename));
}

/* Returns an array with all the scripts presents under the 'scripts' dir. */
function get_script_list() {
	$array = array();

	$dir = opendir("scripts");
	if (!$dir) {
		return $array;
	}
	while($entry = readdir($dir)) {
		if ($entry == "." || $entry == "..") continue;
		$array[] = $entry;
	}
	closedir($dir);
	return $array;
}

/* defaultscript.txt is a template with the default script to create
 * if no existing scripts are presents under the 'scripts' directory */
function create_default_script() {
	copy("defaultscript.txt", "scripts/untitled");
}

function error($msg) {
	global $error;
	$error = "<div style=\"color: black; font-weight: bold; font-size: 14px;\">".
		 "[ERROR]</div>" . $msg;
}

/* Just a security check that will return an error if there is
 * a potentially dangerous character in the file name */
function check_filename_security($name)
{
	if (strstr($name, ".") || strstr($name, "/") || strstr($name, ":") ||
	    strstr($name, "\\")) {
	    	error("Security violation!\n");
		return 1;
	}
	return 0;
}

/* Save the script '$code' under /scripts/$name */
function save_script($name, $code)
{
	if(check_filename_security($name)) return;
	$fd = @fopen("scripts/$name", "w");
	if(!$fd) {
		error("Sorry, I cannot write the file <b>".
		htmlentities($name) . "</b> into <i>scripts</i> directory.<br>".
		"Please, check the directory permissions and try again.");
		return;
	}

	fwrite($fd, $code);
	fclose($fd);
}

/* Load '/scripts/$name' and returns its content. */
function load_script($name)
{
	if(check_filename_security($name)) return "";
	return comp_file_get_contents("scripts/$name");
}

function create_new_tab()
{
	$i = 1;
	while($i < 100) {
		$name = "scripts/untitled$i";
		if (!file_exists($name)) {
			save_script("untitled$i", "echo \"<b>foobar</b>\";");
			return;
		}
		$i++;
	}
}

function delete_script($name)
{
	if(check_filename_security($name)) return;
	if(!@unlink("scripts/$name")) 
		error("Sorry, I cannot remove the file <b>".
		htmlentities($name) . "</b> from <i>scripts</i> directory.<br>".
		"Please, check the directory permissions and try again.");
}

# Rename the script, returns 1 if the new name is colliding with
# an existing script, otherwise the file is renamed and zero returned.
function rename_script($oldname, $newname)
{
	if(check_filename_security($oldname)) return 1;
	if(check_filename_security($newname)) return 1;
	if (file_exists("scripts/$newname")) {
		return 1;
	}
	if(!@rename("scripts/$oldname", "scripts/$newname")) {
		error("Sorry, I cannot rename the file <b>".
		htmlentities($oldname) . "</b> into <i>scripts</i> directory.<br>".
		"Please, check the directory permissions and try again.");
		return 1;
	}

	return 0;
}

/* Program entry */
function main() {
	global $code, $scripts, $current, $doupdate, $output, $donew;
	global $dodelete, $newname, $dorename;

	# Create a new tab if needed
	if (isset($donew)) {
		create_new_tab();
	}
	# Remove the current script if needed
	if (isset($current) && isset($dodelete)) {
		delete_script($current);
		$current = "";
	}
	# Rename the current script if needed
	if (isset($current) && isset($dorename) && isset($newname)) {
		if (rename_script($current, $newname) == 0) 
			$current = $newname;
	}
	# Get the list of scripts, create a default one if none exists.
	$scripts = get_script_list();
	if (count($scripts) == 0) {
		create_default_script();
		$scripts = get_script_list();
	}
	# No current set? use the last script available.
	if (!isset($current) || $current == "") {
		$current = $scripts[(count($scripts))-1];
	}
	# No output mode selected? Our default is html.
	if (!isset($output)) {
		$output = "html";
	}
	# No code? Set it to the default script.
	if (!isset($code)) {
		$code = comp_file_get_contents("defaultscript.txt");
	} 
	//else {
	//	$code = stripslashes($code);
	//}
	# Do the update if needed
	if (isset($doupdate)) {
		save_script($current, $code);
	} else {
		$code = load_script($current);
	}
}

$error = "";

$params = array("lang", "pattern", "current", "output", "newname", "dorename", "dodelete",
	"donew", "doupdate", "code", "scripts");

foreach($params as $p) {
	if(isset($_POST[$p])) $$p = $_POST[$p];
}

main();

?>
<html>
<head>
<title>Php Interactive</title>
<style>
BODY, TD, SPAN, B, UL, LI, DIV, P {
	background-color: white;
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 10px;
}

.selected {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: white;
	color: black;
	border-top: 1px solid black;
	border-left: 1px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	/*width: 1%;*/
}

.unselected {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: #85b1dd;
	color: white;
	border-top: 1px solid black;
	border-left: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 0px solid black;
	/*width: 1%;*/
}

.selected INPUT {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: white;
	color: black;
	border-top: 0px solid black;
	border-left: 0px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	padding: 3px;
	padding-left: 6px;
	padding-right: 6px;

}
.unselected INPUT {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: #85b1dd;
	color: white;
	border-top: 0px solid black;
	border-left: 0px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	padding: 3px;
	padding-left: 6px;
	padding-right: 6px;
}

.rselected {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: white;
	color: black;
	border-top: 1px solid black;
	border-left: 1px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	width: 1%;
}

.runselected {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: #cccccc;
	color: black;
	border-top: 1px solid black;
	border-left: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 0px solid black;
	width: 1%;
}

.rselected INPUT {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: white;
	color: black;
	border: 0px solid black;
	padding: 3px;
	padding-left: 6px;
	padding-right: 6px;

}

.runselected INPUT {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: #cccccc;
	color: black;
	border: 0px solid black;
	padding: 3px;
	padding-left: 6px;
	padding-right: 6px;
}

.search {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: white;
	color: black;
	border: 1px solid black;
	padding: 2px;
}

.ncolumn {
	background-color: #ffffff;
	border-top: 0px solid black;
	border-left: 1px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
}

.bcolumn {
	color: black;
	border-top: 0px solid black;
	border-left: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 0px solid black;
	text-align: center;
}

.bcolumn INPUT {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 14px;
	background-color: #ffffff;
	color: white;
	border-top: 0px solid black;
	border-left: 0px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	padding-left: 6px;
	padding-right: 6px;
	padding-top: 3px;
	padding-bottom: 3px;
	float: center;
}

.rcolumn {
	background-color: white;
	color: black;
	border-color: black;
	border-top: 0px solid black;
	border-left: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 0px solid black;
	/*width: 97%;*/
}

.mcolumn {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 10px;
	padding: 3px;
	padding-top: 15px;
	background-color: white;
	color: black;
	border-color: black;
	border-top: 0px solid black;
	border-left: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 1px solid black;
	width: 100%;
}

.codearea {
	background-color: white;
	color: #437ab2;
	border-top: 1px dashed #660000;
	border-left: 0px solid black;
	border-bottom: 0px solid black;
	border-right: 0px solid black;
	width: 100%;
	padding-top: 10px;
	padding-left: 3px;
	padding-right: 2px;
	padding-bottom: 2px;
	font-family: fixed;
	font-size: 1.8em;
}

.footer {
	font-family: verdana, helvetica, arial, sans-serif;
	font-size: 10px;
	background-color: white;
	color: black;
}

.error {
	color: red;
	padding-top: 14px;
	padding-bottom: 10px;
}

</style>
<script language="JavaScript">
function askDelete() {
    return confirm("Are you sure to delete the current tab?");
}
</script>

</head>
<body>

<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="left" width="40%"><a href="http://www.hping.org/phpinteractive"><img border="0" src="interactive.jpg"></a></td>
<td align="right" width="60%">
  <form method="post" action="http://www.php.net/search.php" name="topsearch">
  <input name="lang" value="en" type="hidden">
  <table cellspacing="0" cellpadding="2" border="0">
    <tr><td>
    <span title="Keyboard shortcut: Alt+S (Win), Ctrl+S (Apple)"><u>s</u>earch&nbsp;for</span></td>
    <td><input class="search" name="pattern" value="" size="30" accesskey="s" type="text"></td>
    <td>in&nbsp;the</td>
    <td><select name="show" class="search">
    <option value="quickref" selected="selected">function list</option><option value="wholesite">whole site</option><option value="manual">online documentation [en]</option><option value="bugdb">bug database</option><option value="maillist">general mailing list</option><option value="devlist">developer mailing list</option><option value="phpdoc">documentation mailing list </option></select></td>
    <td><input src="submit.gif" alt="search" align="bottom" type="image">&nbsp;</td></tr>
  </table>
  </form>
</td>
</tr>
</table>

<? if(!empty($error)) echo "<div class=\"error\">$error</div>"?>
<form name="f" action="<?echo $_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="current" value="<?echo $current?>">
<input type="hidden" name="output" value="<?echo $output?>">
<table border="0" cellpadding="0" cellspacing="0" bgcolor="white" width="100%">
<tr>
 <td>
   <table border="0" cellspacing="0" cellpadding="0" style="border: 0px; border-right: 1px solid black; width: 100%;">
   <tr><?
    foreach($scripts as $script) {
  	  if ($script == $current) {
		$class = "selected";
	  } else {
		$class = "unselected";
	  }
  	  echo("<td class=\"$class\"><input type=\"submit\" name=\"current\" value=\"$script\"></td>");
    }
   ?>
   </tr></table>
 </td>
 <td style="border: 0px; border-bottom: 1px solid black; width: 100%;">&nbsp;</td>
</tr>

<tr>
 <td class="mcolumn" colspan="2">
 Please, type your piece of code below this line:<br>
  <textarea class="codearea" name="code" rows="20"><?
   echo(htmlentities($code))
  ?></textarea>
 </td>
</tr>

<tr>
 <td colspan="2">
   <table class="btable" cellpadding="0" cellspacing="0" width="100%">
   <tr>
   <td class="bcolumn" style="background-color: #ffffff;" width="1%"><input style="color: black; width: 120px; border-top: 1px dashed #666666; border-bottom: 1px dashed #666666;" type="text" name="newname" value="<?php echo $current; ?>"></td>
   <td class="bcolumn" style="background-color: #e3da8e;" width="1%" valign="top"><input style="background-color: #e3da8e; color: black" type="submit" name="dorename" value="rename"></td>
   <td class="ncolumn" width="1%">&nbsp;</td>
   <td class="bcolumn" style="background-color: #ff7b7b;" width="1%" valign="top"><input style="background-color: #ff7b7b;" type="submit" name="dodelete" value="delete" onClick="return askDelete();"></td>
   <td class="ncolumn" width="1%">&nbsp;</td>
   <td class="bcolumn" style="background-color: #85b1dd" width="1%" valign="top" ><input style="background-color: #85b1dd" type="submit" name="donew" value="new tab"></td>
   <td class="ncolumn" width="1%">&nbsp;</td>
   <td title="Keyboard shortcut: Alt+U (Win), Ctrl+U (Apple)" class="bcolumn" style="background-color: #68af6b;" width="1%" valign="top"  align="right"><input style="background-color: #68af6b;" type="submit" name="doupdate" value="update" accesskey="u"></td>
   <td class="ncolumn" width="92%">&nbsp;</td>
   </tr>
   </table>
 </td>
</tr>

</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" bgcolor="white" width="100%">
<tr>
 <td width="100%">
   <table border="0" cellspacing="0" cellpadding="0" width="100%">
   <tr>
   <?
	if ($output == "raw") {
		$class1 = "rselected";
		$class2 = "runselected";
	} else {
		$class1 = "runselected";
		$class2 = "rselected";
	}
	echo("<td class=\"$class1\"><input type=\"submit\" name=\"output\" value=\"raw\"></td>");
	echo("<td class=\"$class2\"><input type=\"submit\" name=\"output\" value=\"html\"></td>");
   ?>
   <td class="rcolumn">&nbsp;</td>
   </tr>
   </table>
 </td>
</tr>

<tr>
 <td class="mcolumn" align="left" width="100%"><?
  ob_start();
  eval($code);
  $script_output = ob_get_contents();
  ob_end_clean();
  if ($output == "raw") {
  	$script_output = "<pre>".htmlentities($script_output)."</pre>";
  }
  echo($script_output);
 ?></td>
</tr>
<tr>
<td class="footer" align="center">
<a href="http://www.hping.org/phpinteractive">phpinteractive</a> -
<a href="http://www.hping.org/visitors">visitors web log analyzer</a> -
<a href="http://www.hping.org/xadsen">xadsen google adsense monitor</a> -
<a href="http://wiki.hping.org">hping security tool</a>
</td>
</tr>
</table>

</form>

</body>
</html>

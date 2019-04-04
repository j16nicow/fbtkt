<?php
//bkmkmnt.php
//User interface. Most HTML files. There are few the PHP cords. 

//location://docroot/.../(web)/bkmkmnt.php

require('bkmkmntApp.php');

$MAX_ROW = 4;

try{
	$app = new bkmkmntApp();
	$app->Execute();
}catch(Exception $e){
	$errstr = $e->getMessage();
	switch($errstr){
		case 'browser err':
			header('Location: _changebrowser.php');	//Example
			exit();
			break;
		case 'login err':
			header('Location: _login.php');	//Example
			exit();
			break;
		default:
			header('Content-Type: text/plain; charset=UTF-8', true, 500);
			echo $errstr;
			exit();
	}
}

function h($str){ return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Entry sample</title>
</head>

<body><br>
//////////////////////////////---- HTML start ----//////////////////////////////<br>
<!-- entry mode select -->
<form method="post" ><?=$app->getLSID()?>debug LSID : <?=$app->getLSIDtest()?>
<dl>
<dt><label for="modename">MODE：</label></dt>
<dd><input type="submit" name="ModeEvent" value="<?=$app->getModeName()?>" /></dd>
</dl>
</form>

<!-- header -->
<form method="post" ><?=$app->getLSID()?>
<dl>
<dt><label for="keycode">KEYCODE：</label></dt>
<dd><input id ="keycode" type="text" size="35" maxlength="32" name="KeyCode" value="<?=h($app->KeyCode)?>" <?=$app->getIsKeyDis()?> /></dd>
<dt><label for="category">CATEGORY：</label></dt>
<dd><input id ="category" type="text" size="35" maxlength="32" name="Category" value="<?=h($app->Category)?>" <?=$app->getIsHedDis()?> /></dd>
</dl>
<br>

<!-- body -->
<table>
<tr><td align="center">row</td><td align="center">name</td><td align="center">genre</td><td align="center">number</td></tr>
<?php
	for($i=0; $i<$MAX_ROW; $i++)
	{
		print '<tr>';
		print '<td>' . $i . '</td>';
		print '<td><input id ="itmname"	type="text" size="35" maxlength="32" name="' . $i . '[ItmName]" value="' . h($app->getItmName($i)) . '" '	. $app->getIsBodDis() . '/></td>';
		print '<td><input id ="genre"	type="text" size="35" maxlength="32" name="' . $i . '[Genre]"	 value="' . h($app->getGenre($i)) . '" '	. $app->getIsBodDis() . '/></td>';
		print '<td><input id ="number"	type="text" size="35" maxlength="32" name="' . $i . '[Number]"	 value="' . h($app->getNumber($i)) . '" '	. $app->getIsBodDis() . '/></td>';
		print '</tr>';
	}
?>
<td></td> <td></td> <td></td> <td><?=$app->getSum()?></td>
</table>
<br>

<!-- tail -->
<input type="submit" name="BackEvent" value="^Back" <?=$app->getIsBakDis()?> />
<input type="submit" name="StepEvent" value="<?=$app->getStepName()?>" />
<br>
<font color="ff0000"><?=$app->getMessage()?><br></font>
</form>

<br>

//////////////////////////////---- HTML  end  ----//////////////////////////////<br><br>
</body>
<?=$app->Dump()?>
</html>

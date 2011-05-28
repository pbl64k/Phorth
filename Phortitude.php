<?php

	/*
		Copyright 2010, 2011 Pavel Lepin

		This file is part of Phorth.
		
		Phorth is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
		
		Phorth is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License
		along with Phorth.  If not, see <http://www.gnu.org/licenses/>.
	*/

	error_reporting(E_ALL | E_STRICT);

	ini_set('max_execution_time', '240');

	require_once(dirname(__FILE__).'/Phorth.php');

	session_start();

	if (isset($_SESSION['PHORTH']) && is_object($_SESSION['PHORTH']) && (! isset($_REQUEST['clean'])))
	{
		$phorth = $_SESSION['PHORTH'];
	}
	else
	{
		$phorth = Phorth::run();
	}

	$before = PhorthEngine___DebugDumper::dumpState($phorth);

	ob_start();

	if (isset($_REQUEST['run']))
	{
		$phorthAfter = clone(eval('return $phorth->'.stripslashes($_REQUEST['code']).';'));
	}
	else
	{
		$phorthAfter = clone($phorth);
	}

	$output = ob_get_contents();

	ob_end_clean();

	$after = PhorthEngine___DebugDumper::dumpState($phorthAfter);

	$result = print_r($phorthAfter->getResult(), TRUE);

	$_SESSION['PHORTH'] = $phorthAfter;

?>
<!DOCTYPE HTML PUBLIC
          "-//W3C//DTD HTML 4.01//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Phortitude</title>
		<style type="text/css">

			body
			{
				font-family: monospace;
			}

			div.engineState
			{
				border: thin solid;
				width: 100%;
				max-width: 100%;
				height: 35em;
				overflow: scroll;
				white-space: pre;
			}

			div.data
			{
				border: thin solid;
				width: 100%;
				max-width: 100%;
				height: 5em;
				overflow: scroll;
				white-space: pre;
			}

		</style>
		<script type="text/javascript">
		</script>
	</head>
	<body onLoad="document.getElementById('code').focus();">
		<form method="POST">
			<table style="width: 100%; max-width: 100%;">
				<tr>
					<th>BEFORE</th>
					<th>AFTER</th>
				</tr>
				<tr>
					<td><div class="engineState"><?php print($before); ?></div></td>
					<td><div class="engineState"><?php print($after); ?></div></td>
				</tr>
				<tr>
					<th>OUTPUT</th>
					<th>RESULT</th>
				</tr>
				<tr>
					<td><div class="data"><?php print($output); ?></div></td>
					<td><div class="data"><?php print($result); ?></div></td>
				</tr>
				<tr>
					<td colspan="2"><input type="text" style="width: 80em;" name="code" id="code"></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="run" value="Run"><input type="submit" name="clean" value="Clean"></td>
				</tr>
			</table>
		</form>
	</body>
</html>

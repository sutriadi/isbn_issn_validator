<?php
/*
 *      isbn_issn_validator.inc.php
 *      
 *      Copyright 2012 Indra Sutriadi Pipii <indra@sutriadi.web.id>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 *      
 *      
 */

if ( ! defined('INDEX_AUTH') || (defined(INDEX_AUTH) AND INDEX_AUTH != 1))
    die("can not access this file directly");

function isbn13($code, $string = false)
{
	$result = false;
	$ints = str_split($code);
	$sum = 0;
	foreach ($ints as $key => $int)
	{
		if ($key != 12)
		{
			if ($key % 2 === 0)
				$sum += $int * 1;
			else
				$sum += $int * 3;
		}
		else
			$checknum = $int;
	}
	$mod10 = fmod(10 - fmod($sum, 10), 10);
	$result = $mod10 == $checknum ? true : false;
	return $string === true ? ($result === true ? 'valid' : 'invalid') : $result;
}

function isbn10($code, $string = false)
{
	$ints = str_split($code);
	$start = 10;
	$sum = 0;
	foreach ($ints as $key => $int)
	{
		if ($key != 9)
			$sum += $int * $start;
		else
			$checknum = $int != 'X' ? $int : 10;
		$start--;
	}
	$mod11 = fmod($sum, 11);
	$result = $mod11 + $checknum == 11 ? true : false;
	return $string === true ? ($result === true ? 'valid' : 'invalid') : $result;
}

function issn($code, $string = false)
{
	$result = false;
	$ints = str_split($code);
	$start = 8;
	$sum = 0;
	foreach ($ints as $key => $int)
	{
		if ($key != 7)
			$sum += $int * $start;
		else
			$checknum = $int != 'X' ? $int : 10;
		$start--;
	}
	$mod11 = fmod($sum, 11);
	if ($mod11 != 0)
		$mod11 = 11 - $mod11;
	$result = $mod11 == $checknum ? true : false;
	return $string === true ? ($result === true ? 'valid' : 'invalid') : $result;
}

$info = __('Use this page to check validity of ISBN/ISSN code');
$validation = '';
if ($_POST)
{

	if ( ! isset($_POST['code']) || trim($_POST['code']) == '')
		$validation = __('No code!');
	else
	{
		$endcode = strtoupper(substr(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING), -1));
		$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT);
		if ($endcode == 'X')
			$code .= $endcode;
		
		if (empty($code)):
			$validation = __('Code invalid!');
		else:
			switch (strlen($code))
			{
				case 13:
					$validation = sprintf(__('Code: %s (ISBN 13) is %s'),
						$code,
						__(isbn13($code, true)));
					break;
				case 10:
					$validation = sprintf(__('Code: %s (ISBN 10) is %s'),
						$code,
						__(isbn10($code, true)));
					break;
				case 8:
					$validation = sprintf(__('Code: %s (ISSN) is %s'),
						$code,
						__(issn($code, true)));
					break;
				default:
					$validation = __('Not ISBN or ISSN');
			}
		endif;
	}

}
?>

	<h3><?php echo __('ISBN/ISSN Validator');?></h3>
	<?php echo $validation;?>
	<form id="regclient" name="regclient" method="POST" action="?p=isbn_issn_validator">
		<p>
			<label for="code">ISBN/ISSN :</label>
			<input type="text" name="code" id="code" maxlength="13" />
		</p>
		<p>
			<input type="submit" value="Validate" />
		</p>
	</form>

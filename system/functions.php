<?php
function check($msg)
{
	if (is_array($msg))
	{
		foreach($msg as $key => $val)
		{
			$msg[$key] = check($val);
		}
	} else {
		$msg = htmlspecialchars($msg);

		//$search = array('|', '\'', '$', '\\', '^', '%', '`', "\0", "\x00", "\x1A", chr(226) . chr(128) . chr(174));
		//$replace = array('&#124;', '&#39;', '&#36;', '&#92;', '&#94;', '&#37;', '&#96;', '', '', '', '');

		//$msg = str_replace($search, $replace, $msg);
		$msg = stripslashes(trim($msg));
	}

	return $msg;
}

<?php

class Sms_Api 
{

	function __construct ()
	{
	
	
	
	}
	
	
    function Send_Sms($mobile,$message,$sender ="",$get_reply=0 )
    {
		global $db,$prefix;
	
		$result = $db->sql_query("SELECT * FROM ".$prefix."_sms_api where api_status='1' order by api_id ASC limit 1");		
		$rows=$db->sql_fetchrow($result);
	
		$api_username = $rows['api_username'];
		$api_password = $rows['api_password'];
		$api_sender = $rows['api_sender'];
		
		$mobile = substr($mobile, 3);

		$message = $this->convert_char($message);

		$message = urlencode($message);
		
		$sms_url = "\$sms_url_final=\"".$rows['api_url']."\";";		
		eval($sms_url);	
		
		$reply = $this->open_url("http://www.qudsnet.com/proxy/sms.php?url=".base64_encode($sms_url_final) );
		
		//echo "<hr>Debug: reply = $reply <hr>";
		if($get_reply == 1) return $reply ;

    }
	
	function convert_char($text, $from="UTF-8", $to="windows-1256")
	{
		 
		 return @iconv($from, $to, $text);
	}

	function open_url($uri)
	{	
		$timeout = 10;
		$parsed_url = @parse_url( $uri );

		if ( !$parsed_url || !is_array( $parsed_url ) )
			return false;

		if ( !isset( $parsed_url['scheme'] ) || !in_array( $parsed_url['scheme'], array( 'http','https' ) ) )
			$uri = 'http://' . $uri;

		if ( @ini_get( 'allow_url_fopen' ) ) {
			$fp = @fopen( $uri, 'r' );
			if ( !$fp )
				return false;

			$linea = '';
			while ( $remote_read = fread( $fp, 4096 ) )
				$linea .= $remote_read;
			fclose( $fp );
			return $linea;
		} elseif ( function_exists( 'curl_init' ) ) {
			$handle = curl_init();
			curl_setopt( $handle, CURLOPT_URL, $uri);
			curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 1 );
			curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );
			$buffer = curl_exec( $handle );
			curl_close( $handle );
			return $buffer;
		} else {
			return false;
		}			
	}
	
}
?>
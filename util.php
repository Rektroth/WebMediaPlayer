<?php 
	class Util
	{
		public function getToken($length)
		{
			$token = "";
			$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
			$codeAlphabet .= "0123456789";
			$max = strlen($codeAlphabet) - 1;
			
			for ($i = 0; $i < $length; $i ++)
			{
				$token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
			}
			
			return $token;
		}
		
		public function cryptoRandSecure($min, $max)
		{
			$range = $max - $min;
			
			if ($range < 1)
			{
				return $min;
			}
			
			$log = ceil(log($range, 2));
			$bytes = (int) ($log / 8) + 1;
			$bits = (int) $log + 1;
			$filter = (int) (1 << $bits) - 1;
			
			do
			{
				$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
				$rnd = $rnd & $filter;
			}
			while ($rnd >= $range);
			
			return $min + $rnd;
		}
		
		public function redirect($url)
		{
			header("Location:" . $url);
			exit;
		}
		
		public function clearAuthCookie()
		{
			if (isset($_COOKIE["username"]))
			{
				setcookie("username", "");
			}
			
			if (isset($_COOKIE["random_password"]))
			{
				setcookie("random_password", "");
			}
			
			if (isset($_COOKIE["random_selector"]))
			{
				setcookie("random_selector", "");
			}
		}
	}
?>
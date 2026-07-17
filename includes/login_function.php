<?php 

if($line == 'local'){
	
		if ( !isset( $_SESSION[ 'uid' ] ) ) {
		  header( "Location: login.php?error=nouser" );
		  exit;
		} else {
		  $now = time(); // Checking the time now when home page starts.

		  $sq1 = "SELECT * FROM users WHERE uid = '".$_SESSION[ 'uid' ]."'";
		  $re1 = $conn1 -> query($sq1);
		  $row1 = $re1 -> fetch_assoc();	
			
			if ( $now > $_SESSION[ 'expire' ] ) {
				session_destroy();
				echo "<script language=\"javascript\">\n";
				echo "alert('Your session has expired!');\n";
				echo "window.location='login.php'";
				echo "</script>";
			  }
  
		
		
		}
	
	
}if($line == 'swknet'){
		if (!isset($_SESSION['USERID'])) {
			header("Location: login.php?error=nouser");
			exit;
		}
		else {
			$now = time(); // Checking the time now when home page starts.
			$Chk = "SELECT * FROM user WHERE email = '".$_SESSION['EMAIL']."'";
			$resChk = $conn1 ->query($Chk);
			$log = $resChk -> fetch_assoc();
			$email = $log['email'];
			$SYSTEM = '3';
			if(in_array($SYSTEM,$sys)){
			if($email == $_SESSION['EMAIL']){				
				if ($now > $_SESSION['expire']) {
					session_destroy();
					echo "<script language=\"javascript\">\n";
					echo "alert('Your session has expired!');\n";
					echo "window.location='login.php'";
					echo "</script>";	 
				}
			}
			}else{
				session_destroy();
				header("Location: login.php?error=nouser");
				exit;
			}
		}
}

?>
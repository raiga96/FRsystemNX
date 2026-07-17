<?php
session_start();
require 'line.php';


switch ( $line ) {
  //LOCALHOST CONNECTION
  case 'local':
if (isset($_POST['cmdLogin'])) {
    require 'connect.php';

    $user = $_POST['txtID'];
    $password = $_POST['txtPassword'];
    $honeypot = $_POST['RoboTest'];

    if (!empty($honeypot)) {
        // Honeypot trap triggered
        $errorMsg = "YOU ARE A ROBOT!";
    } else {
        if (empty($user) || empty($password)) {
            header("Location: ../login.php?error=emptyfield");
            exit();
        } else {
            $sql = "SELECT * FROM users WHERE user_id=? OR user_email=?;";
            $stmt = mysqli_stmt_init($conn1);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../login.php?sqlerror");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $user, $user);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    // Verify password
                    if (password_verify($password, $row['user_password'])) {
                        session_start();
                        $_SESSION['start'] = time(); // Taking now logged in time.
                        $_SESSION['expire'] = $_SESSION['start'] + (600 * 60); // Ending a session in 60 minutes from the starting time.
                        $_SESSION['uid'] = $row['uid'];
                        $_SESSION['user'] = $row['user_id'];

                        // Redirect to appropriate page based on system access
                        $sqlsys = "SELECT * FROM system_access WHERE uid = ?";
                        $ressys = mysqli_stmt_init($conn);
                        if (mysqli_stmt_prepare($ressys, $sqlsys)) {
                            mysqli_stmt_bind_param($ressys, "i", $_SESSION['uid']);
                            mysqli_stmt_execute($ressys);
                            if (mysqli_stmt_fetch($ressys)) {
                                header("Location: ../index.php");
                                exit();
                            } else {
                                header("Location: ../login.php?error=noaccess");
                                exit();
                            }
                        } else {
                            header("Location: ../login.php?error=noaccess");
                            exit();
                        }
                    } else {
                        // Incorrect password
                        header("Location: ../login.php?error=wrongpassword");
                        exit();
                    }
                } else {
                    // User not found
                    header("Location: ../login.php?error=nouser");
                    exit();
                }
            }
        }
    }
} else {
    header("Location: ../login.php");
    exit();
}

    break;
    //SARAWAKNET CONNECTION
  case 'swknet':
    //ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    //ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

    $string = $_POST[ 'txtID' ];
    $sLogin = substr( $string, 0, strrpos( $string, '@' ) );
    if ( $sLogin == '' ) {
      $sLogin = $string;
    }
    //echo "<script type='text/javascript'>alert('$sLogin');</script>";
    //echo $sLogin;

    $ldappass = $_POST[ 'txtPassword' ];

    $ldapconn = ldap_connect( "ldap.sarawak.gov.my" )
    or die( "Could not connect to LDAP server." );

    if ( $ldapconn ) {
      //Base Tree since we don't know the user is from which department
      $dn = "o=Sarawaknet";
      $suid = "uid=" . $sLogin;

      $sr = ldap_search( $ldapconn, $dn, $suid );
      $info = ldap_get_entries( $ldapconn, $sr );

      if ( $info[ "count" ] == 1 ) {
        $ldaprdn = $info[ 0 ][ "dn" ];

        // binding to ldap server
        $ldapbind = ldap_bind( $ldapconn, $ldaprdn, $ldappass );

        // verify binding
        if ( $ldapbind ) {
          //echo "LDAP bind successful...";

          //Get Agency from dn string
          $posStart = strpos( $ldaprdn, "o=" );
          $sFilterDN = substr( $ldaprdn, $posStart );
          $posEnd = strpos( $sFilterDN, "," );
          $sDept = substr( $sFilterDN, 2, $posEnd - 2 );

          $_SESSION[ 'USERID' ] = $sLogin;
          $_SESSION[ 'USERNAME' ] = $info[ 0 ][ "cn" ][ 0 ];
          $_SESSION[ 'EMAIL' ] = $info[ 0 ][ "mail" ][ 0 ];
          $_SESSION[ 'AGENCY' ] = $sDept;
          $_SESSION[ 'start' ] = time(); // Taking now logged in time.
          // Ending a session in 60 minutes from the starting time.
          $_SESSION[ 'expire' ] = $_SESSION[ 'start' ] + ( 60 * 60 );


          //Audit Log Insert here --
          $date = date( 'Y-m-d H:i:s' );
          // ------
          include 'connect.php';
          $sqlA = "SELECT * FROM user WHERE email = '" . $_SESSION[ 'EMAIL' ] . "'";
          $resA = $conn1->query( $sqlA );
          $rowA = $resA->fetch_assoc();
          $_SESSION[ 'uid' ] = $rowA[ 'uid' ];
		  $email = $rowA['email'];
			if($email == $_SESSION['EMAIL']){
          $sysC = '3';
          $sqlC = "SELECT * FROM trans_table WHERE uid = '" . $_SESSION[ 'uid' ] . "'";
          $resC = $conn1->query( $sqlC );
          $rowC = $resC->fetch_array();
          $sys = $rowC[ 'sysid' ];
          //echo $sys;
          $sysid = array( $sys );
          $sysid = explode( ",", $rowC[ 'sysid' ] );
          //Head to landing page
          if ( in_array( $sysC, $sysid ) ) {
            $_SESSION[ 'sys' ] = $sysid;
            header( 'Location: ../home.php?loginsuccess' );
            exit;
          } else {
            session_start();
            session_unset();
            session_destroy();
            header( "Location: ../login.php?error=noaccess" );
            exit();
          }
			}else{
				
				session_start();
            	session_unset();
            	session_destroy();
            	header( "Location: ../login.php?error=not_found".var_dump($email) );
            	exit();
//				echo $_SESSION['EMAIL'];
//				echo $email;
//				echo $_SESSION['uid'];
			}
        } else {
          //wrong password
          echo( "<SCRIPT LANGUAGE='JavaScript'>
				window.alert('Sarawaknet ID and Password mismatch!')
				window.location.href='../login.php';
					</SCRIPT>" );
          exit;
        }
      } else {
        //wrong user id
        echo( "<SCRIPT LANGUAGE='JavaScript'>
				window.alert('User ID does not exist!')
				window.location.href='../login.php';
					</SCRIPT>" );
        exit;
      }

    }
    break;
}


?>

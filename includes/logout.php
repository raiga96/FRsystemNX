<?php
require 'line.php';
switch ( $line ) {
  case 'local':
    session_start();
    session_unset();
    session_destroy();
    header( "Location: ../login.php" );
    break;
  case 'swknet';
  session_start();
  // Finally, destroy the session.

  //Audit Log Insert here
  $date = date( 'Y-m-d H:i:s' );
  //------------------

  session_destroy();
  header( 'Location: ../login.php' );
  break;
}
?>

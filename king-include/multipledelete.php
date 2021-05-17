<?php

require 'king-base.php';
require_once QA_INCLUDE_DIR . 'king-db/selects.php';

$fileName = $_POST['fileid'];
$thumbid  = $_POST['thumbid'];
if( isset( $fileName ) || isset( $thumbid ) ) {
	if (isset($fileName)) {
		$getu = king_get_uploads( $fileName );
		if ( file_exists( $getu['content'] ) ) {
			king_delete_uploads( $fileName );
			unlink( $getu['content'] );
		}
	}
	if ( isset( $thumbid ) ) {
		$gett = king_get_uploads( $thumbid );
		if ( file_exists( $gett['content'] ) ) {
			king_delete_uploads( $thumbid );
			unlink( $gett['content'] );
		}
	}


}

?>
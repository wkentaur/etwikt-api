<?php

/**
 * JSON output type
 */

class FormatJson extends FormatBase {

	private $returnJSONArray;

	function getContentType() {
		if ( $this->api->getParam('callback') ) {
			return 'text/javascript';
		}
		return 'application/json';
	}
	
	function outputBegin($selectedItems) {
		$this->returnJSONArray = array();
		$this->returnJSONArray[] = $this->api->getTopLevelNodeName();
	}

	function outputContinue($row, $continueKey, $primaryKey) {
		$continue = '';
		foreach ( $primaryKey as $key ) {
			$continue .= "|" . rawurlencode( $row->$key );
		}
		$continue = substr( $continue, 1 );
		$this->returnJSONArray["continue"] = array($continueKey=>$continue);	
	}
	
	function outputRow($row, $selectedItems) {
	
		$out = '';
		foreach ( $row as $name => $value ) {
			if ( in_array( $name, $selectedItems ) ) {
				$out = str_replace('_', ' ', $value);
			}
		}
		$toplevel_node_name = $this->api->getTopLevelNodeName();
		$this->returnJSONArray[1][] = $out;	
		
	}

	function outputEnd() {
		$prefix = $suffix = '';
		$callback = $this->api->getParam('callback');
		if ( !is_null( $callback ) and $callback ) {
			$prefix = preg_replace( "/[^][.\\'\\\"_A-Za-z0-9]/", '', $callback ) . '(';
			$suffix = ')';
		}
		echo $prefix . json_encode($this->returnJSONArray) . $suffix;
	}

	function outputErrors( $errors ) {
		$this->outputBegin( false );
		foreach ( (array)$errors as $err ) {
			$this->returnJSONArray['errors'][] = $err;
		}
		$this->outputEnd();
	}
}

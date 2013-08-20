<?php

class ApiMain {

	/**
	 * Mapping of available actions to the class that handles them
	 *   action => API name (class name without 'API' prefix)
	 * NOTE: if you are creating new API actions, you MUST make sure
	 * they are mapped here.
	 * @TODO refactor this list to get registered either by config or somehow
	 *   automagically so new API action creators do not have to edit this file
	 */
	protected static $actionMap = array(
		'opensearch' => 'OpenSearch',
	);

	public static function getActions() {
		return array_keys( self::$actionMap );
	}

	public static function dispatch() {
		Debug::init();

		$api = new ApiDummy;
		try {
			$action = $api->getParam( 'action' );
			if ( $action == 'help' ) {
				self::help();
				return;
			}
			$actionClass = 'Api' . self::$actionMap[$action];
			$obj = new $actionClass;
			$obj->executeModule();
		} catch( Exception $e ) {
			Debug::log( 'Exception: ' . $e->getMessage() );
			$format = $api->getFormatter();
			$format->headers();
			$format->outputErrors( $e->getMessage() );
		}

		Debug::saveLog();
	}

	/**
	 * Print a help message
	 * @TODO build this dynamically
	 */
	public static function help() {
		/* TODO: Expand me and generate automagically! */
	}
}

/**
 * Dummy API class to expose some functionality from ApiBase that is useful
 * to us before we know which API class to invoke.
 */
class ApiDummy extends ApiBase {
	protected function executeModule() {
		return;
	}

	public function getAllowedParams() {
		return $this->getDefaultAllowedParams();
	}
}

?>

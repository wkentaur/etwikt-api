<?php

$wgEnableOpenSearchSuggest = false;
const NS_MAIN = 0;

class ApiOpenSearch extends ApiBase {
	private $lang_cats = array(
		'en'  => 'Inglise'
    );

	public function __construct() {
		$this->setTopLevelNodeName( $this->getParam( 'search' ) );
		$this->setObjectNodeName( 'opensearch' );
	}

	public function executeModule() {
		global $wgEnableOpenSearchSuggest;    
		
		$search = $this->getParam( 'search' );
		$lang = $this->getParam( 'lang' );
		$limit = $this->getParam( 'limit' );
		#$namespaces = $this->getParam( 'namespace' );
		$suggest = $this->getParam( 'suggest' );
		$db = Database::getDb();
		$tables = array();

		if ( $search ) {
			$search = trim($search);
			$search = str_replace('%', '', $search);
			$search = str_replace(' ', '_', $search);
			$search = $search . '%';
			$where[] = 'LOWER(page_title) LIKE ' .
					$db->quote( $search );
			$where[] = 'page_namespace  = ' . NS_MAIN;
			$orderby = array('page_title');
			$fields = array('page_title');
			$tables[] = 'page';
			$forceIndex = false;
			if ( $lang AND $this->lang_cats[$lang] ) { 
				$tables[] = 'categorylinks';
				$cat = $this->lang_cats[$lang];
				$where[] = 'page.page_id  = categorylinks.cl_from';
				$where[] = 'categorylinks.cl_to = ' . $db->quote( $cat );
				# "SELECT cl_from FROM categorylinks WHERE cl_from = '%s' AND cl_to = '%s'"
		    }
			$res = $db->select( $fields,  $tables, $where,
			 $orderby, $limit, $forceIndex );
			
			$formatter = $this->getFormatter();
			$formatter->output( $res, $limit, 'srcontinue', $fields, $orderby );
		}

	}

	public function getAllowedParams() {
		return array(
			'format' => array( 
			    ApiBase::PARAM_DFLT => 'json', 
    			ApiBase::PARAM_TYPE =>  array( 'json', 'xml', 'xmlfm' ),
			),
			'callback' => array( 
			    ApiBase::PARAM_DFLT => false, 
			    ApiBase::PARAM_TYPE => 'callback' 
			),
			'search' => array( 
				ApiBase::PARAM_DFLT => false, 
				ApiBase::PARAM_TYPE => 'string' 
			),
			'lang' => array( 
				ApiBase::PARAM_DFLT => false, 
				ApiBase::PARAM_TYPE => array( 'en' ),
			),
			'limit' => array(
				ApiBase::PARAM_DFLT => 10,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => 100,
			),
			'namespace' => array(
				ApiBase::PARAM_DFLT => NS_MAIN,
				ApiBase::PARAM_TYPE => 'namespace',
				ApiBase::PARAM_ISMULTI => true
			),
			'suggest' => false,
			'source' => false,
		);
	}

	public function getParamDescription() {
		return array(
			'search' => 'Search string',
			'limit' => 'Maximum amount of results to return',
			'namespace' => 'Namespaces to search',
			'suggest' => 'Do nothing if $wgEnableOpenSearchSuggest is false',
		);
	}

	public function getDescription() {
		return 'Search the wiki using the OpenSearch protocol';
	}

	public function getExamples() {
		return array(
			'api.php?action=opensearch&search=Te'
		);
	}

	public function getHelpUrls() {
		return 'https://www.mediawiki.org/wiki/API:Opensearch';
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}

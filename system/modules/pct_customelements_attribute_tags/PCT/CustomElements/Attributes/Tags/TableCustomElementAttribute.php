<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2014, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @attribute	AttributeTags
 * @link		http://contao.org
 */

/**
 * Namespace
 */
namespace PCT\CustomElements\Attribute\Tags;

/**
 * Imports
 */
use PCT\CustomElements\Helper\ControllerHelper as ControllerHelper;

/**
 * Class file
 * TableCustomElementTags
 */
class TableCustomElementAttribute extends \Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	
	/**
	 * Set the tabletree source
	 * @param object
	 */
	public function setSourceTable(\DataContainer $objDC)
	{
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		
		if(!$objActiveRecord->tag_custom || strlen($objActiveRecord->tag_table) < 1)
		{
			return;
		}
		
		$GLOBALS['TL_DCA'][$objDC->table]['fields']['tag_roots']['eval']['source'] = $objActiveRecord->tag_table;
			
	}
} 
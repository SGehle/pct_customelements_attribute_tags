<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2014, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements_attribute_tags
 * @link		http://contao.org
 * @license     LGPL
 */

/**
 * Namespace
 */
namespace PCT\CustomElements\Attributes;

/**
 * Imports
 */
use PCT\CustomElements\Helper\ControllerHelper as ControllerHelper;

/**
 * Class file
 * Tags
 */
class Tags extends \PCT\CustomElements\Core\Attribute
{
	/**
	 * Tell the vault how to save the data (binary,blob)
	 * Leave empty to varchar
	 * @var boolean
	 */
	protected $saveDataAs = 'blob';
	
	/**
	 * Return the field definition
	 * @return array
	 */
	public function getFieldDefinition()
	{
		$arrEval = $this->getEval();

		if($this->get('eval_multiple') > 0)
		{
			$arrEval['fieldType'] ='checkbox';
			$arrEval['multiple'] = true;
		}
		
		$arrReturn = array
		(
			'label'			=> array( $this->get('title'),$this->get('description') ),
			'exclude'		=> true,
			'inputType'		=> 'pct_tabletree',
			'tabletree'		=> array
			(
				'source'		=> 'tl_pct_customelement_tags',
				'valueField'	=> 'title',
				'keyField'		=> 'id',
			),
			'eval'			=> $arrEval,
			'sql'			=> "blob NULL",
		);
		
		// use a custom source
		if($this->get('tag_custom'))
		{
			$arrReturn['tabletree']['source'] = $this->get('tag_table');
			$arrReturn['tabletree']['valueField'] = $this->get('tag_value');
			$arrReturn['tabletree']['keyField'] = $this->get('tag_key');
			$arrReturn['tabletree']['sortingField'] = $this->get('tag_sorting');
		}
		
		// set root nodes
		$arrReturn['tabletree']['roots'] = deserialize($this->get('tag_roots'));
		
		// make field sortable
		#$arrReturn['sortable'] = true;
		
		return $arrReturn;
	}
	
	
	/**
	 * Parse widget callback
	 * Generate the widgets in the backend 
	 * @param object	Widget
	 * @param string	Name of the field
	 * @param array		Field definition
	 * @param object	DataContainer
	 * @return string	HTML output of the widget
	 */
	public function parseWidgetCallback($objWidget,$strField,$arrFieldDef,$objDC)
	{
		$arrFieldDef['id'] = $arrFieldDef['strField'] = $arrFieldDef['name'] = $strField;
		$arrFieldDef['strTable'] = $objDC->table;
		// recreate the widget since contao does not support custom config/eval arrays for widgets yet
		$objWidget = new $GLOBALS['BE_FFL']['pct_tabletree']($arrFieldDef);
		$objWidget->label = $this->get('title');
		$objWidget->description = $this->get('description');
		
		// validate the input
		$objWidget->validate();
		
		if($objWidget->hasErrors())
		{
			$objWidget->class = 'error';
		}
		
		return $objWidget->parse();
	}



	/**
	 * Generate the attribute in the frontend
	 * @param string
	 * @param mixed
	 * @param array
	 * @param string
	 * @param object
	 * @param object
	 * @return string
	 * called renderCallback method
	 */
	public function renderCallback($strField,$varValue,$objTemplate,$objAttribute)
	{
		$varValue = deserialize($varValue);
		
		if(empty($varValue) || count($varValue) < 1)
		{
			return '';
		}
		
		if(is_array($varValue))
		{
			$varValue = array($varValue);
		}
		
		// fetch the readable values
		$strSource = 'tl_pct_customelement_tags';
		$strValueField = 'title';
		$strKeyField = 'id';
		$strSorting = 'sorting';
		if($objAttribute->get('tag_custom'))
		{
			$strSource = $objAttribute->get('tag_table');
			$strValueField = $objAttribute->get('tag_value');
			if($objAttribute->get('tag_key')) {$strKeyField = $objAttribute->get('tag_key');}
			$strSortingField = $objAttribute->get('tag_sorting');
		}
		
		$objResult = $objDatabase->prepare("SELECT * FROM ".$strSource." WHERE ".$objDatabase->findInSet($strKeyField,$varValue).($strSorting ? " ORDER BY ".$strSorting:"") )->execute();
		if($objResult->numRows < 1)
		{
			return '';
		}
		$objTemplate->result = $objResult;
		$objTemplate->value = implode(',', $objResult->fetchEach($strValueField) );
		return $objTemplate->parse();
	}
	
	
	/**
	 * Return the options as array
	 * @return array()
	 */
	public function getSelectOptions()
	{
		$objOrigin = $this->getOrigin();
		$objDatabase = \Database::getInstance();
		$strField = $this->get('alias');
		
		$objRows = $objDatabase->prepare("SELECT * FROM ".$objOrigin->getTable()." WHERE ".$strField. " IS NOT NULL")->execute();
		if($objRows->numRows < 1)
		{
			return array();
		}
		
		$arrValues = array();
		while($objRows->next())
		{
			$values = deserialize($objRows->{$strField});
			if(!is_array($values))
			{
				$values = array($values);
			}
			$arrValues = array_merge($arrValues,$values);
		}
		
		// fetch the readable values
		$strSource = 'tl_pct_customelement_tags';
		$strValueField = 'title';
		$strKeyField = 'id';
		$strSorting = 'sorting';
		if($this->get('tag_custom'))
		{
			$strSource = $this->get('tag_table');
			$strValueField = $this->get('tag_value');
			if($this->get('tag_key')) {$strKeyField = $this->get('tag_key');}
			$strSortingField = $this->get('tag_sorting');
		}
		
		$objResult = $objDatabase->prepare("SELECT * FROM ".$strSource." WHERE ".$objDatabase->findInSet($strKeyField, $arrValues).($strSorting ? " ORDER BY ".$strSorting:"") )->execute();
		if($objResult->numRows < 1)
		{
			return array();
		}
		
		$arrReturn = array();
		while($objResult->next())
		{
			$arrReturn[$objResult->{$strKeyField}] = $objResult->{$strValueField};
		}
		
		return $arrReturn;
	}
	
	
	/**
	 * Custom backend filtering routing
	 * @param array
	 * @param string
	 * @param object
	 * @param object
	 */
	public function getBackendFilterOptions($arrData,$strField,$objAttribute,$objCC)
	{
		$arrOptions = $objAttribute->getSelectOptions();
		if(count($arrOptions) < 1)
		{
			return array();
		}
		
		$strTable = $objCC->getTable();
	
		$objRows = \Database::getInstance()->prepare("SELECT * FROM ".$strTable." WHERE ".$strField. " IS NOT NULL")->execute();
		if($objRows->numRows < 1)
		{
			return array();
		}
		
		$arrSession = \Session::getInstance()->getData();
		$strSession = $GLOBALS['PCT_CUSTOMCATALOG']['backendFilterSession'];
		
		$varFilterValue = $arrSession[$strSession][$strTable][$strField] ?: $arrSession['filter'][$strTable][$strField];
		$varSearchValue = $arrSession[$strSession.'_search'][$strTable]['value'] ?: $arrSession['search'][$strTable]['value'];
		
		$arrIds = array();
		while($objRows->next())
		{
			$values = deserialize($objRows->{$strField});
			if(!is_array($values))
			{
				$values = array($values);
			}
			
			if(!in_array($varSearchValue, $values) && !in_array($varFilterValue, $values))
			{
				continue;
			}
			
			$arrIds[] = $objRows->id;
		}
		
		if(count($arrIds) < 1)
		{
			return array();
		}
		
		return array('id IN(?)',implode(',',$arrIds));
	}
	
	
	/**
	 * Modify the field DCA settings for customcatalogs
	 * @param array
	 * @param string
	 * @param object
	 * @param object
	 * @param object
	 * @param object
	 * @return array
	 */	
	public function prepareField($arrData,$fieldName,$objAttribute,$objCC,$objCE,$objSystemIntegration)
	{
		if($objAttribute->get('type') != 'tags')
		{
			return $arrData;
		}
		
		// set the orgin to the customcatalog
		$objAttribute->setOrigin($objCC);
		
		$arrData['fieldDef']['options'] = $objAttribute->getSelectOptions();
		
		return $arrData;
	}
	
}
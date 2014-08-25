<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2014, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @subpackage	pct_customelements_plugin_tags
 * @link		http://contao.org
 */

/**
 * Constants
 */
define(PCT_CUSTOMELEMENTS_TAGS_PATH, 'system/modules/pct_customelements_attribute_tags');
define(PCT_CUSTOMELEMENTS_TAGS_VERSION, '1.0.0');

/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], count($GLOBALS['BE_MOD']['content']), array
(
	'pct_customelements_tags' => array
	(
		'tables' 		=> array('tl_pct_customelement_tags'),
		'icon'   		=> PCT_CUSTOMELEMENTS_TAGS_PATH.'/assets/img/tags_mod.png',
	)
));


/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['pct_TableTree'] = 'PCT\Widgets\WidgetTableTree';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('PCT\Widgets\TableTree\TableTreeHelper','postActions');
$GLOBALS['TL_HOOKS']['executePreActions'][] = array('PCT\Widgets\TableTree\TableTreeHelper','preActions');

/**
 * Register attribute
 */
$GLOBALS['PCT_CUSTOMELEMENTS']['ATTRIBUTES']['tags'] = array
(
	'path' 		=> 'system/modules/pct_customelements_attributetags',
	'class'		=> 'PCT\CustomElements\Attributes\Tags',
	'icon'		=> 'fa fa-tags'
);

/**
 * Register filter
 */
$GLOBALS['PCT_CUSTOMELEMENTS']['FILTERS']['tags'] = array
(
	'path' 		=> 'system/modules/pct_customelements_attributetags',
	'class'		=> 'PCT\CustomElements\Filters\Tags',
	'icon'		=> 'fa fa-tags'
);
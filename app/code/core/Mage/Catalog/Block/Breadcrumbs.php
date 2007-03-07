<?php



/**
 * Breadcrumbs block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Array of bread crumbs
     * 
     * array(
     *  [$index] => array(
     *                  ['label']
     *                  ['title']
     *                  ['link']
     *              )
     * )
     *
     * @var array
     */
    protected $_crumbs=null;
    
    function __construct()
    {
    	parent::__construct();
    	$this->setViewName('Mage_Catalog', 'breadcrumbs');
    }
    
    function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link'));
    	$this->_crumbs[$crumbName]=$crumbInfo;
    }
    
    function toString()
    {
    	$this->assign('crumbs', $this->_crumbs);
    	return parent::toString();
    }
}
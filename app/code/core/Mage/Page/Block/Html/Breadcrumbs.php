<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      ??? <???@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Page_Block_Html_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Array of breadcrumbs
     * 
     * array(
     *  [$index] => array(
     *                  ['label']
     *                  ['title']
     *                  ['link']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_crumbs = null;
    
    function __construct()
    {
    	parent::__construct();
    	$this->setTemplate('page/html/breadcrumbs.phtml');
    }
    
    function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link', 'last'));
    	$this->_crumbs[$crumbName]=$crumbInfo;
    }
    
    function toHtml()
    {
        if (is_array($this->_crumbs)) {
            $last_crumb = array_pop($this->_crumbs);
            $last_crumb['last'] = true;
            array_push($this->_crumbs, $last_crumb);
        }
    	$this->assign('crumbs', $this->_crumbs);
    	return parent::toHtml();
    }
}

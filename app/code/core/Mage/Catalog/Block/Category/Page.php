<?php
/**
 * Category landing page
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Category_Page extends Mage_Core_Block_Template
{
    protected $_cmsBlock;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/category/page.phtml');
    }
    
    protected function _initChildren()
    {
        $this->_cmsBlock = Mage::getModel('cms/block')
            ->load(Mage::registry('current_category')->getLandingPage());
    }
    
    public function getContent()
    {
        return $this->_cmsBlock->getContent();
    }
    
    public function getTitle()
    {
        return $this->_cmsBlock->getTitle();
    }
}

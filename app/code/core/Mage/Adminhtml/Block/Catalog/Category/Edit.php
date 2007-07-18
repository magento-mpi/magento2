<?php
/**
 * Category edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Edit extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/catalog/category/edit.phtml');
    }
    
    protected function _initChildren()
    {
        $this->append(
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );
    }
    
    public function getHeader()
    {
        return __('New Category');
    }
}

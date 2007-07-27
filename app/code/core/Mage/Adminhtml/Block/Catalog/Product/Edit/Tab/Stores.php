<?php
/**
 * Product Stores tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Stores extends Mage_Adminhtml_Block_Store_Switcher
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/stores.phtml');
    }
}

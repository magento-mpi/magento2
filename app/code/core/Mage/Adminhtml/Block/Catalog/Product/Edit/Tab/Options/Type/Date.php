<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Date extends
    Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/edit/options/type/date.phtml');
    }

}

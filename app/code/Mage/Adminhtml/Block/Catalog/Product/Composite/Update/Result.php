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
 * Adminhtml block for result of catalog product composite update
 * Forms response for a popup window for a case when form is directly submitted
 * for single item
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Update_Result extends Mage_Core_Block_Template
{
    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = Mage::registry('composite_update_result');
        $resultJson = Mage::helper('Mage_Core_Helper_Data')->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return Mage::helper('Mage_Adminhtml_Helper_Js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}

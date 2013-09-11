<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for result of catalog product composite update
 * Forms response for a popup window for a case when form is directly submitted
 * for single item
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Composite\Update;

class Result extends \Magento\Core\Block\Template
{
    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = \Mage::registry('composite_update_result');
        $resultJson = \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return \Mage::helper('Magento\Adminhtml\Helper\Js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}

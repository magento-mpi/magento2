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
class Magento_Adminhtml_Block_Catalog_Product_Composite_Update_Result extends Magento_Core_Block_Template
{
    /**
     * Adminhtml js
     *
     * @var Magento_Adminhtml_Helper_Js
     */
    protected $_adminhtmlJs = null;

    /**
     * @param Magento_Adminhtml_Helper_Js $adminhtmlJs
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Js $adminhtmlJs,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = Mage::registry('composite_update_result');
        $resultJson = $this->_coreData->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return $this->_adminhtmlJs->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}

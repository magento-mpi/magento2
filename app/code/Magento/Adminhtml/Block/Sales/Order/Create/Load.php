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
 * Adminhtml sales order create newsletter block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Load extends Magento_Core_Block_Template
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

    protected function _toHtml()
    {
        $result = array();
        $layout = $this->getLayout();
        foreach ($this->getChildNames() as $name) {
            $result[$name] = $layout->renderElement($name);
        }
        $resultJson = $this->_coreData->jsonEncode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return $this->_adminhtmlJs->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
        } else {
            return $resultJson;
        }
    }
}

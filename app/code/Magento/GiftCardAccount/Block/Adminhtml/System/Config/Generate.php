<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GiftCardAccount_Block_Adminhtml_System_Config_Generate
    extends Magento_Backend_Block_System_Config_Form_Field
{

    protected $_template = 'config/generate.phtml';

    /**
     * Pool factory
     *
     * @var Magento_GiftCardAccount_Model_Pool_Factory
     */
    protected $_poolFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_GiftCardAccount_Model_Pool_Factory $poolFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        Magento_GiftCardAccount_Model_Pool_Factory $poolFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $application, $data);
        $this->_poolFactory = $poolFactory;
    }

    /**
     * Get the button and scripts contents
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return Magento_Object
     */
    public function getUsage()
    {
        return $this->_poolFactory->create()->getPoolUsageInfo();
    }
}

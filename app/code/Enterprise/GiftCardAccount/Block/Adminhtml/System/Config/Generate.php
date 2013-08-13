<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_GiftCardAccount_Block_Adminhtml_System_Config_Generate extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * @var string
     */
    protected $_template = 'config/generate.phtml';

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_App $application
     * @param Mage_Backend_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_App $application,
        Mage_Backend_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_urlModel = $urlModel;
        parent::__construct($context, $application, $data);
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
        return Mage::getModel('Enterprise_GiftCardAccount_Model_Pool')->getPoolUsageInfo();
    }

    /**
     * Get url model
     *
     * @return Mage_Backend_Model_Url
     */
    public function getUrlModel()
    {
        return $this->_urlModel;
    }
}

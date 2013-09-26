<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Action group checkboxes renderer for system configuration
 */
class Magento_Logging_Block_Adminhtml_System_Config_Actions
    extends Magento_Backend_Block_System_Config_Form_Field
{
    protected $_template = 'system/config/actions.phtml';

    /**
     * @var Magento_Logging_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Logging_Model_Config $config
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Logging_Model_Config $config,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Action group labels getter
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->_config->getLabels();
    }

    /**
     * Check whether specified group is active
     *
     * @param string $key
     * @return bool
     */
    public function getIsChecked($key)
    {
        return $this->_config->isEventGroupLogged($key);
    }

    /**
     * Render element html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Email_Template extends Magento_Object
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Core_Model_Registry
     */
    private $_coreRegistry;

    /**
     * @var Magento_Core_Model_Email_Template_Config
     */
    private $_emailConfig;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Email_Template_Config $emailConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Email_Template_Config $emailConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_coreRegistry = $coreRegistry;
        $this->_emailConfig = $emailConfig;
    }

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Magento_Core_Model_Resource_Email_Template_Collection $collection */
        if (!$collection = $this->_coreRegistry->registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('Magento_Core_Model_Resource_Email_Template_Collection')
                ->load();
            $this->_coreRegistry->register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateId = str_replace('/', '_', $this->getPath());
        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, array(
            'value' => $templateId,
            'label' => $templateLabel
        ));
        return $options;
    }
}

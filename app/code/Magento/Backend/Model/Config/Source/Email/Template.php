<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config config system template source
 */
class Magento_Backend_Model_Config_Source_Email_Template extends Magento_Object
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Resource_Email_Template_CollectionFactory
     */
    protected $_templatesFactory;

    /**
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Resource_Email_Template_CollectionFactory $templatesFactory
     * @param Magento_Core_Model_ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Resource_Email_Template_CollectionFactory $templatesFactory,
        Magento_Core_Model_ConfigInterface $config,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_templatesFactory = $templatesFactory;
        $this->_config = $config;
        parent::__construct($data);
    }

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$collection = $this->_coreRegistry->registry('config_system_email_template')) {
            /** @var $collection Magento_Core_Model_Resource_Email_Template_Collection */
            $collection = $this->_templatesFactory->create();
            $collection->load();
            $this->_coreRegistry->register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = __('Default Template');
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = $this->_config->getNode(
            Magento_Core_Model_Email_Template::XML_PATH_TEMPLATE_EMAIL . '/' . $nodeName . '/label'
        );
        if ($templateLabelNode) {
            $templateName = __('%1 (Default)', __((string)$templateLabelNode));
        }
        array_unshift( $options, array(
            'value' => $nodeName,
            'label' => $templateName
        ));
        return $options;
    }
}

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
 * Config edit page
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_System_Config_Edit extends Magento_Backend_Block_Widget
{
    const DEFAULT_SECTION_BLOCK = 'Magento_Backend_Block_System_Config_Form';

    /**
     * Form block class name
     *
     * @var string
     */
    protected $_formBlockName;

    /**
     * Block template File
     *
     * @var string
     */
    protected $_template = 'Magento_Backend::system/config/edit.phtml';

    /**
     * Configuration structure
     *
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Backend_Model_Config_Structure $configStructure
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Backend_Model_Config_Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->_configStructure = $configStructure;
    }

    /**
     * Prepare layout object
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        /** @var $section Magento_Backend_Model_Config_Structure_Element_Section */
        $section = $this->_configStructure->getElement($this->getRequest()->getParam('section'));
        $this->_formBlockName = $section->getFrontendModel();
        if (empty($this->_formBlockName)) {
            $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setTitle($section->getLabel());
        $this->setHeaderCss($section->getHeaderCss());

        $this->addChild('save_button', 'Magento_Backend_Block_Widget_Button', array(
            'label'     => __('Save Config'),
            'class' => 'save primary',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#config-edit-form'),
                ),
            ),
        ));
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve rendered save buttons
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve config save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_config_save/index', array('_current' => true));
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config edit page
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Edit extends Mage_Backend_Block_Widget
{
    const DEFAULT_SECTION_BLOCK = 'Mage_Backend_Block_System_Config_Form';

    /**
     * Sections configuration
     *
     * @var array
     */
    protected $_section;

    /**
     * @var Mage_Backend_Model_Config_StructureInterface
     */
    protected $_systemConfig;

    /**
     * Block template File
     *
     * @var string
     */
    protected $_template = 'Mage_Backend::system/config/edit.phtml';

    protected  function _construct()
    {
        $this->_systemConfig = $this->hasData('systemConfig') ?
            $this->getData('systemConfig') :
            Mage::getSingleton('Mage_Backend_Model_Config_Structure_Reader')->getConfiguration();

        parent::_construct();

        $sectionCode = $this->getRequest()->getParam('section');

        $this->_section = $this->_systemConfig->getSection($sectionCode);

        $this->setTitle($this->_section['label']);
        $this->setHeaderCss(isset($this->_section['header_css']) ? $this->_section['header_css'] : '');
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => $this->helper('Mage_Backend_Helper_Data')->__('Save Config'),
            'onclick'   => 'configForm.submit()',
            'class' => 'save',
        ));
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * @return Mage_Backend_Block_System_Config_Edit
     */
    public function initForm()
    {
        $blockName = isset($this->_section['frontend_model']) ? $this->_section['frontend_model'] : '';
        if (empty($blockName)) {
            $blockName = self::DEFAULT_SECTION_BLOCK;
        }

        $block = $this->getLayout()->createBlock($blockName);
        $block->initForm();
        $this->setChild('form', $block);
        return $this;
    }
}

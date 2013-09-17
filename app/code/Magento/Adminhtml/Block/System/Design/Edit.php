<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_System_Design_Edit extends Magento_Adminhtml_Block_Widget
{

    protected $_template = 'system/design/edit.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('design_edit');
    }

    protected function _prepareLayout()
    {
        $this->addChild('back_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
            'class' => 'back'
        ));

        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Save'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#design-edit-form'),
                ),
            ),
        ));

        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Delete'),
            'onclick'   => 'confirmSetLocation(\'' . __('Are you sure?') . '\', \'' . $this->getDeleteUrl() . '\')',
            'class'  => 'delete'
        ));
        return parent::_prepareLayout();
    }

    public function getDesignChangeId()
    {
        return $this->_coreRegistry->registry('design')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getHeader()
    {
        if ($this->_coreRegistry->registry('design')->getId()) {
            $header = __('Edit Design Change');
        } else {
            $header = __('New Store Design Change');
        }
        return $header;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\System\Design;

class Edit extends \Magento\Backend\Block\Widget
{

    protected $_template = 'Magento_Backend::system/design/edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('design_edit');
    }

    protected function _prepareLayout()
    {
        $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/*/') . '\')',
            'class' => 'back'
        ));

        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Save'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#design-edit-form'),
                ),
            ),
        ));

        $this->addChild('delete_button', 'Magento\Backend\Block\Widget\Button', array(
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
        return $this->getUrl('adminhtml/*/delete', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('adminhtml/*/save', array('_current'=>true));
    }

    public function getValidationUrl()
    {
        return $this->getUrl('adminhtml/*/validate', array('_current'=>true));
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

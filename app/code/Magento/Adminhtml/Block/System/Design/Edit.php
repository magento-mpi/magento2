<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\System\Design;

class Edit extends \Magento\Adminhtml\Block\Widget
{

    protected $_template = 'system/design/edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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
        $this->addChild('back_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
            'class' => 'back'
        ));

        $this->addChild('save_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Save'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#design-edit-form'),
                ),
            ),
        ));

        $this->addChild('delete_button', 'Magento\Adminhtml\Block\Widget\Button', array(
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

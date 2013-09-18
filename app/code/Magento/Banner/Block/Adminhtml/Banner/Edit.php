<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml\Banner;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
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

    /**
     * Initialize banner edit page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Banner'));
        $this->_updateButton('delete', 'label', __('Delete Banner'));

        $this->_addButton('save_and_edit_button', array(
                'label'   => __('Save and Continue Edit'),
                'class'   => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
        ), 100);
    }

    /**
     * Get current loaded banner ID
     *
     */
    public function getBannerId()
    {
        return $this->_coreRegistry->registry('current_banner')->getId();
    }

    /**
     * Get header text for banenr edit page
     *
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_banner')->getId()) {
            return $this->escapeHtml($this->_coreRegistry->registry('current_banner')->getName());
        } else {
            return __('New Banner');
        }
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}

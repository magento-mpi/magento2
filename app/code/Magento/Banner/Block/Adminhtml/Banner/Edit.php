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

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize banner edit page. Set management buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Banner'));
        $this->_updateButton('delete', 'label', __('Delete Banner'));

        $this->_addButton(
            'save_and_edit_button',
            array(
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array('button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'))
                )
            ),
            100
        );
    }

    /**
     * Get current loaded banner ID
     *
     * @return mixed
     */
    public function getBannerId()
    {
        return $this->_registry->registry('current_banner')->getId();
    }

    /**
     * Get header text for banner edit page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_registry->registry('current_banner')->getId()) {
            return $this->escapeHtml($this->_registry->registry('current_banner')->getName());
        } else {
            return __('New Banner');
        }
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('adminhtml/*/save');
    }
}

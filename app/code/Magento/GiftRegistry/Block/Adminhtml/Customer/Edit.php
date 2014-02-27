<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer;

class Edit
    extends \Magento\Backend\Block\Widget\Form\Container
{
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

    /**
     * Initialize form
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_GiftRegistry';
        $this->_controller = 'adminhtml_customer';

        parent::_construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        $confirmMessage = __('Are you sure you want to delete this gift registry?');
        $this->_updateButton('delete', 'label', __('Delete Registry'));
        $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->escapeJsQuote($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $entity = $this->_coreRegistry->registry('current_giftregistry_entity');
        if ($entity->getId()) {
            return $this->escapeHtml($entity->getTitle());
        }
        return __('Gift Registry Entity');
    }

    /**
     * Retrieve form back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $customerId = null;
        if ($this->_coreRegistry->registry('current_giftregistry_entity')) {
            $customerId = $this->_coreRegistry->registry('current_giftregistry_entity')->getCustomerId();
        }
        return $this->getUrl('customer/index/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }
}

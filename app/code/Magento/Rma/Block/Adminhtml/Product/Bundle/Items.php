<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Product\Bundle;

/**
 * Additional Renderer of Product's Attribute Enable RMA control structure
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize current rma bundle item
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setItems($this->_coreRegistry->registry('current_rma_bundle_item'));
        $this->setParentId((int)$this->getRequest()->getParam('item_id'));
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
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
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Registry $registry,
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

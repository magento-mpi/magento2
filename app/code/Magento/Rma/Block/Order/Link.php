<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Order;

/**
 * "Returns" link
 */
class Link extends \Magento\Sales\Block\Order\Link
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaHelper;

    /**
     * Rma grid collection
     *
     * @var \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\Registry $registry,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Rma\Helper\Data $rmaHelper,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_rmaHelper = $rmaHelper;
        parent::__construct($context, $defaultPath, $registry, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isRmaAviable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get is link aviable
     *
     * @return bool
     */
    protected function _isRmaAviable()
    {
        if ($this->_rmaHelper->isEnabled()) {
            /** @var $collection \Magento\Rma\Model\Resource\Rma\Grid\Collection */
            $collection = $this->_collectionFactory->create();
            $returns = $collection->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'order_id',
                $this->_registry->registry('current_order')->getId()
            )->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}

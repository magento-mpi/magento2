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
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaHelper;

    /**
     * Constructor
     *
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Rma\Helper\Data $rmaHelper,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        $this->_rmaHelper = $rmaHelper;
        parent::__construct($context, $registry, $coreData, $data);
    }

    /**
     * @inheritdoc
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
     * @return bool
     */
    protected function _isRmaAviable()
    {
        if ($this->_rmaHelper->isEnabled()) {
            $returns = \Mage::getResourceModel('Magento\Rma\Model\Resource\Rma\Grid\Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', $this->_registry->registry('current_order')->getId())
                ->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}

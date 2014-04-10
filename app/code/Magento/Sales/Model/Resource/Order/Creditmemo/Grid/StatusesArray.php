<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Grid;

/**
 * Sales creditmemo statuses option array
 */
class StatusesArray implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\Order\CreditmemoFactory
     */
    protected $creditmemoFactory;

    /**
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     */
    public function __construct(\Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory)
    {
        $this->creditmemoFactory = $creditmemoFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->creditmemoFactory->create()->getStates();
    }
}

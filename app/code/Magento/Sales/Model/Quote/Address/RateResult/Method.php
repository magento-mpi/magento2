<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address\RateResult;

/**
 * Fields:
 * - carrier: carrier code
 * - carrierTitle: carrier title
 * - method: carrier method
 * - methodTitle: method title
 * - price: cost+handling
 * - cost: cost
 */
class Method extends AbstractResult
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, array $data = array())
    {
        $this->_storeManager = $storeManager;
        parent::__construct($data);
    }

    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->setData('price', $this->_storeManager->getStore()->roundPrice($price));
        return $this;
    }
}

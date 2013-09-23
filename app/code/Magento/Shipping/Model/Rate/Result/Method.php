<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Fields:
 * - carrier: ups
 * - carrierTitle: United Parcel Service
 * - method: 2day
 * - methodTitle: UPS 2nd Day Priority
 * - price: $9.40 (cost+handling)
 * - cost: $8.00
 */
class Magento_Shipping_Model_Rate_Result_Method extends Magento_Shipping_Model_Rate_Result_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($data);
    }

    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return Magento_Shipping_Model_Rate_Result_Method
     */
    public function setPrice($price)
    {
        $this->setData('price', $this->_storeManager->getStore()->roundPrice($price));
        return $this;
    }
}

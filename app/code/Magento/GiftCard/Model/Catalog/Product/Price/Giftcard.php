<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Catalog_Product_Price_Giftcard extends Magento_Catalog_Model_Product_Type_Price
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cached amounts
     * @var array
     */
    protected $_amountCache = array();

    /**
     * Cached minimum and maximal amounts
     * @var array
     */
    protected $_minMaxCache = array();

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($eventManager);
    }


    /**
     * Return price of the specified product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getPrice($product)
    {
        if ($product->getData('price')) {
            return $product->getData('price');
        } else {
            return 0;
        }
    }

    /**
     * Retrieve product final price
     *
     * @param integer $qty
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty=null, $product)
    {
        $finalPrice = $product->getPrice();
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('giftcard_amount');
            if ($customOption) {
                $finalPrice += $customOption->getValue();
            }
        }
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        $product->setData('final_price', $finalPrice);
        return max(0, $product->getData('final_price'));
    }

    /**
     * Load and set gift card amounts into product object
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function getAmounts($product)
    {
        $allGroups = Magento_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('giftcard_amounts');

        if (is_null($prices)) {
            if ($attribute = $product->getResource()->getAttribute('giftcard_amounts')) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('giftcard_amounts');
            }
        }

        return ($prices) ? $prices : array();
    }


    /**
     * Return minimal amount for Giftcard product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getMinAmount($product)
    {
        $minMax = $this->_calcMinMax($product);
        return $minMax['min'];
    }

    /**
     * Return maximal amount for Giftcard product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getMaxAmount($product)
    {
        $minMax = $this->_calcMinMax($product);
        return $minMax['max'];
    }

    /**
     * Fill in $_amountCache or return precalculated sorted values for amounts
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getSortedAmounts($product)
    {
        if (!isset($this->_amountCache[$product->getId()])) {
            $result = array();

            $giftcardAmounts = $this->getAmounts($product);
            if (is_array($giftcardAmounts)) {
                foreach ($giftcardAmounts as $amount) {
                    $result[] = $this->_storeManager->getStore()->roundPrice($amount['website_value']);
                }
            }
            sort($result);
            $this->_amountCache[$product->getId()] = $result;
        }
        return $this->_amountCache[$product->getId()];
    }

    /**
     * Fill in $_minMaxCache or return precalculated values for min, max
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    protected function _calcMinMax($product)
    {
        if (!isset($this->_minMaxCache[$product->getId()])) {
            $min = $max = null;
            if ($product->getAllowOpenAmount()) {
                $openMin = $product->getOpenAmountMin();
                $openMax = $product->getOpenAmountMax();

                if ($openMin) {
                    $min = $openMin;
                } else {
                    $min = 0;
                }
                if ($openMax) {
                    $max = $openMax;
                } else {
                    $max = 0;
                }
            }

            foreach ($this->getSortedAmounts($product) as $amount) {
                if ($amount) {
                    if (is_null($min)) {
                        $min = $amount;
                    }
                    if (is_null($max)) {
                        $max = $amount;
                    }

                    $min = min($min, $amount);
                    if ($max != 0) {
                        $max = max($max, $amount);
                    }
                }
            }

            $this->_minMaxCache[$product->getId()] = array('min'=>$min, 'max'=>$max);
        }
        return $this->_minMaxCache[$product->getId()];
    }
}

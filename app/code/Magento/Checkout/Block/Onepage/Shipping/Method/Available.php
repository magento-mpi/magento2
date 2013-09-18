<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage\Shipping\Method;

class Available extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    protected $_rates;
    protected $_address;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($configCacheType, $coreData, $context, $data);
    }

    public function getShippingRates()
    {

        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
            /*
            if (!empty($groups)) {
                $ratesFilter = new \Magento\Filter\Object\Grid();
                $ratesFilter->addFilter(\Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            */

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_storeConfig->getConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(
            $this->_taxData->getShippingPrice($price, $flag, $this->getAddress()),
            true
        );
    }
}

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
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $resourceSession
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     * @param \Magento\Tax\Helper\Data $taxData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $resourceSession,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory,
        \Magento\Tax\Helper\Data $taxData,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct(
            $context,
            $coreData,
            $configCacheType,
            $customerSession,
            $resourceSession,
            $countryCollFactory,
            $regionCollFactory,
            $data
        );
    }

    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $this->_rates = $this->getAddress()->getGroupedAllShippingRates();        }

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

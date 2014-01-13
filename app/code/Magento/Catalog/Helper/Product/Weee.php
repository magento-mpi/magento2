<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Weee helper
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

class Weee extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeData;

    /**
     * @param \Magento\Weee\Helper\Data $weeeData
     */
    public function __construct(
        \Magento\Weee\Helper\Data $weeeData
    ) {
        $this->weeeData = $weeeData;
    }

    /**
     * Get weee tax amount for product based on shipping and billing addresses, website and tax settings
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @param   null|\Magento\Customer\Model\Address\AbstractAddress $shipping
     * @param   null|\Magento\Customer\Model\Address\AbstractAddress $billing
     * @param   mixed $website
     * @param   bool $calculateTaxes
     * @return  float
     */
    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        $this->weeeData->getAmount(
            $product,
            $shipping = null,
            $billing = null,
            $website = null,
            $calculateTaxes = false
        );
    }

    /**
     * Returns all summed WEEE taxes with all local taxes applied
     *
     * @throws \Magento\Exception
     * @param array $attributes Array of \Magento\Object, result from getProductWeeeAttributes()
     * @return float
     */
    public function getAmountInclTaxes($attributes)
    {
        $this->weeeData->getAmountInclTaxes($attributes);
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        $this->weeeData->isTaxable($store);
    }

    /**
     * Get Product Weee attributes for price renderer
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|false|\Magento\Object $shipping Shipping Address
     * @param null|false|\Magento\Object $billing Billing Address
     * @param null|\Magento\Core\Model\Website $website
     * @param mixed $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributesForRenderer(
        $product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false
    ) {
        $this->weeeData->getProductWeeeAttributesForRenderer(
            $product, $shipping, $billing, $website, $calculateTaxes
        );
    }

    /**
     * Returns diaplay type for price accordingly to current zone
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array|null                 $compareTo
     * @param string                     $zone
     * @param \Magento\Core\Model\Store      $store
     * @return bool|int
     */
    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        $this->weeeData->typeOfDisplay($product, $compareTo, $zone, $store);
    }

    /**
     * Return array of WEEE attributes allowed for display
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductWeeeAttributesForDisplay($product)
    {
        $this->weeeData->getProductWeeeAttributesForDisplay($product);
    }

    /**
     * Returns amount to display
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getAmountForDisplay($product)
    {
        $this->weeeData->getAmountForDisplay($product);
    }

    /**
     * Get weee amount display type on product view page
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        $this->weeeData->getPriceDisplayType($store);
    }

    /**
     * Returns original amount
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getOriginalAmount($product)
    {
        $this->weeeData->getOriginalAmount($product);
    }
}
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Product Links part block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Catalog\Product;

class Links extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($registry, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->getProduct()->getLinksPurchasedSeparately();
    }

    /**
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return $this->getProduct()->getTypeInstance()
            ->getLinkSelectionRequired($this->getProduct());
    }

    /**
     * @return boolean
     */
    public function hasLinks()
    {
        return $this->getProduct()->getTypeInstance()
            ->hasLinks($this->getProduct());
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->getProduct()->getTypeInstance()
            ->getLinks($this->getProduct());
    }

    /**
     * @param Magento_Downloadable_Model_Link $link
     *
     * @param \Magento\Downloadable\Model\Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link)
    {
        $price = $link->getPrice();
        $store = $this->getProduct()->getStore();

        if (0 == $price) {
            return '';
        }

        $taxCalculation = \Mage::getSingleton('Magento\Tax\Model\Calculation');
        if (!$taxCalculation->getCustomer() && $this->_coreRegistry->registry('current_customer')) {
            $taxCalculation->setCustomer($this->_coreRegistry->registry('current_customer'));
        }

        $taxHelper = $this->_taxData;
        $coreHelper = $this->helper('Magento\Core\Helper\Data');
        $_priceInclTax = $taxHelper->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = $taxHelper->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceInclTax, $store);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+'.$coreHelper
                    ->currencyByStore($_priceInclTax, $store).' '.__('Incl. Tax').')';
            }
        }
        $priceStr .= '</span>';

        return $priceStr;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->getProduct()->getStore();
        return $this->helper('Magento\Core\Helper\Data')->currencyByStore($price, $store, false);
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        $coreHelper = $this->_coreData;

        foreach ($this->getLinks() as $link) {
            $config[$link->getId()] = $coreHelper->currency($link->getPrice(), false, false);
        }

        return $coreHelper->jsonEncode($config);
    }

    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('downloadable/download/linkSample', array('link_id' => $link->getId()));
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->getProduct()->getLinksTitle()) {
            return $this->getProduct()->getLinksTitle();
        }
        return $this->_storeConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return $this->_storeConfig->getConfigFlag(Magento_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

    /**
     * Returns whether link checked by default or not
     *
     * @param \Magento\Downloadable\Model\Link $link
     * @return bool
     */
    public function getIsLinkChecked($link)
    {
        $configValue = $this->getProduct()->getPreconfiguredValues()->getLinks();
        if (!$configValue || !is_array($configValue)) {
            return false;
        }

        return $configValue && (in_array($link->getId(), $configValue));
    }

    /**
     * Returns value for link's input checkbox - either 'checked' or ''
     *
     * @param \Magento\Downloadable\Model\Link $link
     * @return string
     */
    public function getLinkCheckedValue($link)
    {
        return $this->getIsLinkChecked($link) ? 'checked' : '';
    }
}

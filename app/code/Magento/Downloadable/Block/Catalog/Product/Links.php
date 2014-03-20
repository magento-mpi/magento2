<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Block\Catalog\Product;

use Magento\Downloadable\Model\Link;

/**
 * Downloadable Product Links part block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Links extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_calculationModel;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $coreData;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Tax\Model\Calculation $calculationModel
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Tax\Model\Calculation $calculationModel,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Core\Helper\Data $coreData,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_calculationModel = $calculationModel;
        $this->jsonEncoder = $jsonEncoder;
        $this->coreData = $coreData;
        parent::__construct(
            $context,
            $data,
            $priceBlockTypes
        );
        $this->_isScopePrivate = true;
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
        return $this->getProduct()->getTypeInstance()->getLinkSelectionRequired($this->getProduct());
    }

    /**
     * @return boolean
     */
    public function hasLinks()
    {
        return $this->getProduct()->getTypeInstance()->hasLinks($this->getProduct());
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->getProduct()->getTypeInstance()->getLinks($this->getProduct());
    }

    /**
     * @param Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link)
    {
        $price = $link->getPrice();
        $store = $this->getProduct()->getStore();

        if (0 == $price) {
            return '';
        }

        $taxCalculation = $this->_calculationModel;
        if (!$taxCalculation->getCustomer() && $this->_coreRegistry->registry('current_customer')) {
            $taxCalculation->setCustomer($this->_coreRegistry->registry('current_customer'));
        }

        $taxHelper = $this->_taxData;
        $coreHelper = $this->coreData;
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
                $priceStr .= ' (+' . $coreHelper->currencyByStore(
                    $_priceInclTax,
                    $store
                ) . ' ' . __(
                    'Incl. Tax'
                ) . ')';
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
        return $this->coreData->currencyByStore($price, $store, false);
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getLinks() as $link) {
            $config[$link->getId()] = $this->coreData->currency($link->getPrice(), false, false);
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * @param Link $link
     * @return string
     */
    public function getLinkSamlpeUrl($link)
    {
        $store = $this->getProduct()->getStore();
        return $store->getUrl('downloadable/download/linkSample', array('link_id' => $link->getId()));
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
        return $this->_storeConfig->getConfig(Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return $this->_storeConfig->getConfigFlag(Link::XML_PATH_TARGET_NEW_WINDOW);
    }

    /**
     * Returns whether link checked by default or not
     *
     * @param Link $link
     * @return bool
     */
    public function getIsLinkChecked($link)
    {
        $configValue = $this->getProduct()->getPreconfiguredValues()->getLinks();
        if (!$configValue || !is_array($configValue)) {
            return false;
        }

        return $configValue && in_array($link->getId(), $configValue);
    }

    /**
     * Returns value for link's input checkbox - either 'checked' or ''
     *
     * @param Link $link
     * @return string
     */
    public function getLinkCheckedValue($link)
    {
        return $this->getIsLinkChecked($link) ? 'checked' : '';
    }
}

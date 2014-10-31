<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Checkout\Cart\Item;

use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shopping cart item render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * @var Configuration
     */
    protected $_configurationHelper = null;

    /**
     * Bundle catalog product configuration
     *
     * @var Configuration
     */
    protected $_bundleProdConfigur = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Configuration $bundleProdConfigur
     * @param \Magento\Msrp\Helper\Data $msrpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Core\Helper\Url $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        Configuration $bundleProdConfigur,
        \Magento\Msrp\Helper\Data $msrpHelper,
        array $data = array()
    ) {
        $this->_bundleProdConfigur = $bundleProdConfigur;
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageHelper,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $msrpHelper,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_configurationHelper = $this->_bundleProdConfigur;
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @param bool $useCache
     * @return array
     */
    protected function _getBundleOptions($useCache = true)
    {
        return $this->_configurationHelper->getBundleOptions($this->getItem());
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param \Magento\Catalog\Model\Product $selectionProduct
     * @return float
     */
    protected function _getSelectionFinalPrice($selectionProduct)
    {
        $helper = $this->_bundleProdConfigur;
        $result = $helper->getSelectionFinalPrice($this->getItem(), $selectionProduct);
        return $result;
    }

    /**
     * Get selection quantity
     *
     * @param int $selectionId
     * @return float
     */
    protected function _getSelectionQty($selectionId)
    {
        return $this->_configurationHelper->getSelectionQty($this->getProduct(), $selectionId);
    }

    /**
     * Overloaded method for getting list of bundle options
     * Caches result in quote item, because it can be used in cart 'recent view' and on same page in cart checkout
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_configurationHelper->getOptions($this->getItem());
    }

    /**
     * Return cart item error messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array('text' => $message, 'type' => $quoteItem->getHasError() ? 'error' : 'notice');
            }
        }

        return $messages;
    }
}

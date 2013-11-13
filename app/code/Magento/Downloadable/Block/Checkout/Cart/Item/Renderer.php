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
 * Shopping cart downloadable item render block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Checkout\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{

    /**
     * Downloadable catalog product configuration
     *
     * @var \Magento\Downloadable\Helper\Catalog\Product\Configuration
     */
    protected $_downloadProdConfig = null;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Downloadable\Helper\Catalog\Product\Configuration $dwnCtlgProdConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Downloadable\Helper\Catalog\Product\Configuration $dwnCtlgProdConfig,
        array $data = array()
    ) {
        $this->_downloadProdConfig = $dwnCtlgProdConfig;
        parent::__construct($context, $coreData, $productConfig, $checkoutSession, $data);
    }

    /**
     * Retrieves item links options
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_downloadProdConfig->getLinks($this->getItem());
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle()
    {
        return $this->_downloadProdConfig->getLinksTitle($this->getProduct());
    }
}

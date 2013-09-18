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
     * @param \Magento\Downloadable\Helper\Catalog\Product\Configuration $dwnCtlgProdConfig
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfiguration
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Downloadable\Helper\Catalog\Product\Configuration $dwnCtlgProdConfig,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_downloadProdConfig = $dwnCtlgProdConfig;
        parent::__construct($productConfiguration, $coreData, $context, $data);
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

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
class Magento_Downloadable_Block_Checkout_Cart_Item_Renderer extends Magento_Checkout_Block_Cart_Item_Renderer
{

    /**
     * Downloadable catalog product configuration
     *
     * @var Magento_Downloadable_Helper_Catalog_Product_Configuration
     */
    protected $_downloadProdConfig = null;

    /**
     * @param Magento_Catalog_Helper_Product_Configuration $productConfiguration
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Downloadable_Helper_Catalog_Product_Configuration $dwnCtlgProdConfig
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Configuration $productConfiguration,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Downloadable_Helper_Catalog_Product_Configuration $dwnCtlgProdConfig,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_downloadProdConfig = $dwnCtlgProdConfig;
        parent::__construct($productConfiguration, $coreData, $context, $checkoutSession, $data);
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

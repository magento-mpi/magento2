<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product price xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Price extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Default product price renderer block factory name
     *
     * @var string
     */
    protected $_defaultPriceRenderer = 'Mage_XmlConnect_Block_Catalog_Product_Price_Default';

    /**
     * Store supported product price xml renderers based on product types
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Store already initialized renderers instances
     *
     * @var array
     */
    protected $_renderersInstances = array();

    /**
     * Add new product price renderer
     *
     * @param string $type
     * @param string $renderer
     * @return Mage_XmlConnect_Block_Product_Options
     */
    public function addRenderer($type, $renderer)
    {
        if (!isset($this->_renderers[$type])) {
            $this->_renderers[$type] = $renderer;
        }
        return $this;
    }

    /**
     * Collect product prices to current xml object
     */
    public function collectProductPrices()
    {
        $product = $this->getProduct();
        $xmlObject = $this->getProductXmlObj();

        if ($product && $product->getId()) {
            $type = $product->getTypeId();
            if (isset($this->_renderers[$type])) {
                $blockName = $this->_renderers[$type];
            } else {
                $blockName = $this->_defaultPriceRenderer;
            }

            $renderer = $this->getLayout()->getBlock($blockName);
            if (!$renderer) {
                $renderer = $this->getLayout()->createBlock($blockName);
            }

            if ($renderer) {
                $renderer->collectProductPrices($product, $xmlObject);
            }
        }
    }
}

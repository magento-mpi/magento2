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
 * Helper for fetching properties by product configurational item
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Helper\Catalog\Product;

class Configuration extends \Magento\Core\Helper\AbstractHelper
    implements \Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface
{
    /**
     * Catalog product configuration
     *
     * @var Magento_Catalog_Helper_Product_Configuration
     */
    protected $_productConfigur = null;

    /**
     * @param Magento_Catalog_Helper_Product_Configuration $productConfigur
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Configuration $productConfigur,
        Magento_Core_Helper_Context $context
    ) {
        $this->_productConfigur = $productConfigur;
        parent::__construct($context);
    }

    /**
     * Retrieves item links options
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getLinks(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $product = $item->getProduct();
        $itemLinks = array();
        $linkIds = $item->getOptionByCode('downloadable_link_ids');
        if ($linkIds) {
            $productLinks = $product->getTypeInstance()
                ->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }
        }
        return $itemLinks;
    }

    /**
     * Retrieves product links section title
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getLinksTitle($product)
    {
        $title = $product->getLinksTitle();
        if (strlen($title)) {
            return $title;
        }
        return \Mage::getStoreConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Retrieves product options
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $options = $this->_productConfigur->getOptions($item);

        $links = $this->getLinks($item);
        if ($links) {
            $linksOption = array(
                'label' => $this->getLinksTitle($item->getProduct()),
                'value' => array()
            );
            foreach ($links as $link) {
                $linksOption['value'][] = $link->getTitle();
            }
            $options[] = $linksOption;
        }

        return $options;
    }
}

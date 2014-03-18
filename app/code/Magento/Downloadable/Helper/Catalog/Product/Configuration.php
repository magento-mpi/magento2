<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Helper\Catalog\Product;

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Configuration extends \Magento\App\Helper\AbstractHelper
    implements \Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface
{
    /**
     * Catalog product configuration
     *
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $_productConfigur = null;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfigur
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfigur,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        $this->_productConfigur = $productConfigur;
        $this->_storeConfig = $coreStoreConfig;
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
        return $this->_storeConfig->getValue(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
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

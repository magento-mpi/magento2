<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Link attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Attribute;

class Link extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_GoogleShopping_Helper_Data $gsData,
        Magento_GoogleShopping_Helper_Product $gsProduct,
        Magento_GoogleShopping_Helper_Price $gsPrice,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_GoogleShopping_Model_Resource_Attribute $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($gsData, $gsProduct, $gsPrice, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $url = $product->getProductUrl(false);
        if ($url) {
            if (!$this->_coreStoreConfig->getConfigFlag('web/url/use_store')) {
                $urlInfo = parse_url($url);
                $store = $product->getStore()->getCode();
                if (isset($urlInfo['query']) && $urlInfo['query'] != '') {
                    $url .= '&___store=' . $store;
                } else {
                    $url .= '?___store=' . $store;
                }
            }

            $links = $entry->getLink();
            if (!is_array($links)) {
                $links = array();
            }
            $link = $entry->getService()->newLink();
            $link->setHref($url);
            $link->setRel('alternate');
            $link->setType('text/html');
            if ($product->getName()) {
                $link->setTitle($product->getName());
            }
            $links[0] = $link;
            $entry->setLink($links);
        }

        return $entry;
    }
}

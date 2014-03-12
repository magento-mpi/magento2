<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer;

use Magento\CatalogSearch\Model\Resource\EngineProvider;

class AvailabilityFlag extends \Magento\Catalog\Model\Layer\Category\AvailabilityFlag
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param EngineProvider $engineProvider
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        EngineProvider $engineProvider
    ) {
        $this->storeManager = $storeManager;
        $this->engineProvider = $engineProvider;
    }

    /**
     * Is filter enabled
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @param array $filters
     * @return bool
     */
    public function isEnabled($layer, $filters)
    {
        $_isLNAllowedByEngine = $this->engineProvider->get()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)$this->storeManager->getStore()->getConfig(self::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount > $layer->getProductCollection()->getSize())) {
            return parent::isEnabled($layer, $filters);
        }
        return false;
    }
} 

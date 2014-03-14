<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag;

use Magento\CatalogSearch\Model\Resource\EngineProvider;

class Plugin
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
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Layer $layer
     * @param array $filters
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsEnabled(
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $subject, \Closure $proceed, $layer, $filters
    ) {
        $_isLNAllowedByEngine = $this->engineProvider->get()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)$this->storeManager->getStore()->getConfig(self::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount > $layer->getProductCollection()->getSize())) {
            return $proceed($layer, $filters);
        }
        return false;
    }
} 

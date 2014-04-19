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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param EngineProvider $engineProvider
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        EngineProvider $engineProvider
    ) {
        $this->scopeConfig = $scopeConfig;
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
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $subject,
        \Closure $proceed,
        $layer,
        $filters
    ) {
        $_isLNAllowedByEngine = $this->engineProvider->get()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)$this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_LAYER_COUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$availableResCount || ($availableResCount > $layer->getProductCollection()->getSize())) {
            return $proceed($layer, $filters);
        }
        return false;
    }
}

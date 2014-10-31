<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Price;

use Magento\Catalog\Model\Layer\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Range
{
    const XML_PATH_RANGE_STEP = 'catalog/layered_navigation/price_range_step';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Category
     */
    private $layer;

    /**
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param Category $layer
     * @internal param \Magento\Framework\Registry $registry
     */
    public function __construct(Registry $registry, ScopeConfigInterface $scopeConfig, Category $layer)
    {
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->layer = $layer;
    }

    /**
     * @return array
     */
    public function getPriceRange()
    {
        $currentCategory = $this->registry->registry('current_category_filter') ?: $this->layer->getCurrentCategory();
        return $currentCategory->getFilterPriceRange();
    }

    /**
     * @return float
     */
    public function getConfigRangeStep()
    {
        return (double)$this->scopeConfig->getValue(self::XML_PATH_RANGE_STEP, ScopeInterface::SCOPE_STORE);
    }
}
 
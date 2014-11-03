<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Dynamic;

use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\Layer\Filter\Price\Render;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Search\Dynamic\Algorithm;
use Magento\Store\Model\ScopeInterface;

class Improved implements AlgorithmInterface
{
    const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';

    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @var Category
     */
    private $layer;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Render
     */
    private $render;

    /**
     * @param Algorithm $algorithm
     * @param Category $layer
     * @param ScopeConfigInterface $scopeConfig
     * @param Render $render
     */
    public function __construct(Algorithm $algorithm, Category $layer, ScopeConfigInterface $scopeConfig, Render $render)
    {
        $this->algorithm = $algorithm;
        $this->layer = $layer;
        $this->scopeConfig = $scopeConfig;
        $this->render = $render;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData(array $intervals = [], $additionalRequestData = '')
    {
        $collection = $this->layer->getProductCollection();
        $appliedInterval = $intervals;
        if ($appliedInterval && $collection->getPricesCount() <= $this->getIntervalDivisionLimit()) {
            return array();
        }
        $this->algorithm->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getPricesCount()
        );

        if ($appliedInterval) {
            if ($appliedInterval[0] == $appliedInterval[1] || $appliedInterval[1] === '0') {
                return array();
            }
            $this->algorithm->setLimits($appliedInterval[0], $appliedInterval[1]);
        }

        $items = array();
        foreach ($this->algorithm->calculateSeparators() as $separator) {
            $items[] = array(
                'label' => $this->render->renderRangeLabel($separator['from'], $separator['to']),
                'value' => ($separator['from'] == 0 ? ''
                        : $separator['from']) . '-' . $separator['to'] . $additionalRequestData,
                'count' => $separator['count']
            );
        }

        return $items;
    }

    /**
     * Get interval division limit
     *
     * @return int
     */
    private function getIntervalDivisionLimit()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_INTERVAL_DIVISION_LIMIT, ScopeInterface::SCOPE_STORE);
    }
}

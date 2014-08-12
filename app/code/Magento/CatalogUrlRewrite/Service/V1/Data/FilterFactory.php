<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\Data;

use Magento\Framework\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\FilterInterface;

class FilterFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $filterClasses;

    /**
     * @param ObjectManager $objectManager
     * @param array $filterClasses
     */
    public function __construct(
        ObjectManager $objectManager,
        $filterClasses = []
    ) {
        $this->filterClasses = $filterClasses;
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $filterData
     * @return FilterInterface
     * @throws \Exception
     */
    public function create(array $filterData = [])
    {
        if (!$filterData) {
            throw new \Exception('No filterData');
        } elseif (empty($filterData['entity_type'])) {
            throw new \Exception('Type is empty');
        } elseif (empty($this->filterClasses[$filterData['entity_type']])) {
            throw new \Exception('Undefined filter class');
        }

        $filterObject = $this->objectManager->create(
            $this->filterClasses[$filterData['entity_type']],
            ['filterData' => $filterData]
        );
        if (!$filterObject instanceof FilterInterface) {
            throw new \Exception('Invalidate filter object');
        }

        return $filterObject;
    }
}

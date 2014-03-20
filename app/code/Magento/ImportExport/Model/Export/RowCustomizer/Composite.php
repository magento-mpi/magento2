<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Export\RowCustomizer;

use Magento\ImportExport\Model\Export\RowCustomizerInterface;
use Magento\ObjectManager;

class Composite implements RowCustomizerInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $customizers;

    /**
     * @param ObjectManager $objectManager
     * @param array $customizers
     */
    public function __construct(ObjectManager $objectManager, $customizers = array())
    {
        $this->objectManager = $objectManager;
        $this->customizers = $customizers;
    }

    /**
     * Prepare data for export
     *
     * @param mixed $collection
     * @param int $productIds
     * @return mixed|void
     */
    public function prepareData($collection, $productIds)
    {
        foreach ($this->customizers as $className) {
            $this->objectManager->get($className)->prepareData($collection, $productIds);
        }
    }

    /**
     * Set headers columns
     *
     * @param array $columns
     * @return array
     */
    public function addHeaderColumns($columns)
    {
        foreach ($this->customizers as $className) {
            $columns = $this->objectManager->get($className)->addHeaderColumns($columns);
        }
        return $columns;
    }

    /**
     * Add data for export
     *
     * @param array $dataRow
     * @param int $productId
     * @return array
     */
    public function addData($dataRow, $productId)
    {
        foreach ($this->customizers as $className) {
            $dataRow = $this->objectManager->get($className)->addData($dataRow, $productId);
        }
        return $dataRow;
    }

    /**
     * Calculate the largest links block
     *
     * @param array $additionalRowsCount
     * @param int $productId
     * @return array|mixed
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        foreach ($this->customizers as $className) {
            $additionalRowsCount = $this->objectManager->get(
                $className
            )->getAdditionalRowsCount(
                $additionalRowsCount,
                $productId
            );
        }
        return $additionalRowsCount;
    }
}

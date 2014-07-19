<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Storage\Data;

use \Magento\Framework\Service\DataObjectConverter;

/**
 * Data object converter
 */
class Converter
{
    /**
     * @var \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractBuilderFactory
     */
    protected $dataBuilderFactory;

    /**
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractBuilderFactory $dataBuilderFactory
     */
    public function __construct(AbstractBuilderFactory $dataBuilderFactory)
    {
        $this->dataBuilderFactory = $dataBuilderFactory;
    }

    /**
     * Convert array to Storage Data object
     *
     * @param array $data
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function convertArrayToObject(array $data)
    {
        $dataBuilder = $this->dataBuilderFactory->create();
        return $dataBuilder->populateWithArray($data)->create();
    }

    /**
     * Convert array of Storage Data objects to array
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractData[] $storageDataObjects
     * @return array
     */
    public function convertObjectsToArray(array $storageDataObjects)
    {
        $flatData = [];
        foreach ($storageDataObjects as $objectData) {
            $flatData[] = DataObjectConverter::toFlatArray($objectData);
        }
        return $flatData;
    }
}

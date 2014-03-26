<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Repository;

use Magento\ObjectManager;

/**
 * Class CollectionProvider
 *
 * @package Mtf\Util\Generate\Repository
 */
class CollectionProvider implements CollectionProviderInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get collection by type which is specified in fixture .xml
     *
     * @param array $fixture
     * @return array
     */
    public function getCollection(array $fixture)
    {
        $type = $fixture['type'];
        $method = $type . 'Collection';
        if (!method_exists($this, $method)) {
            return [];
        }

        $collection = $this->$method($fixture);

        $keys = !empty($fixture['data_set']) && is_array($fixture['data_set'])
            ? array_keys($fixture['data_set'])
            : [];

        $items = [];
        foreach ($collection as $model) {
            /** @var $model \Magento\Object */
            $item = $model->toArray($keys);
            $item['id'] = $model->getId();
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get collection of objects for which eav or flat type is not exist
     *
     * @param array $fixture
     * @return \Magento\Object[]
     */
    protected function tableCollection(array $fixture)
    {
        $collection = $fixture['collection'];
        $collection = $this->objectManager->create($collection, ['fixture' => $fixture]);
        /** @var $collection \Mtf\Util\Generate\Repository\TableCollection */
        return $collection->getItems();
    }

    /**
     * Get collection of objects for flat type
     *
     * @param array $fixture
     * @return \Magento\Object[]
     */
    protected function flatCollection(array $fixture)
    {
        $collection = $fixture['collection'];
        $collection = $this->objectManager->create($collection);
        /** @var $collection \Magento\Model\Resource\Db\Collection\AbstractCollection */
        $collection->addFieldToSelect('*');

        return $collection->getItems();
    }

    /**
     * Get collection of objects for eav type
     *
     * @param array $fixture
     * @return \Magento\Object[]
     */
    protected function eavCollection(array $fixture)
    {
        $collection = $fixture['collection'];
        $collection = $this->objectManager->create($collection);
        /** @var $collection \Magento\Eav\Model\Entity\Collection\AbstractCollection */
        $collection->addAttributeToSelect('*');
        if (isset($fixture['product_type'])) {
            $collection->addAttributeToFilter('type_id', $fixture['product_type']);
        }

        return $collection->getItems();
    }
}

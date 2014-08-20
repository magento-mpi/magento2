<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

class ObjectRegistry
{
    /**
     * Key is id of entity, value is entity
     *
     * @var \Magento\Framework\Object[]
     */
    protected $entitiesMap;

    /**
     * @param \Magento\Framework\Object[] $entities
     */
    public function __construct($entities)
    {
        $entitiesMap = [];
        foreach ($entities as $entity) {
            $entitiesMap[$entity->getId()] = $entity;
        }
        $this->entitiesMap = $entitiesMap;
    }

    /**
     * @param int $entityId
     * @return \Magento\Framework\Object|null
     */
    public function get($entityId)
    {
        return isset($this->entitiesMap[$entityId]) ? $this->entitiesMap[$entityId] : null;
    }

    /**
     * @return \Magento\Framework\Object[]
     */
    public function getList()
    {
        return $this->entitiesMap;
    }
}

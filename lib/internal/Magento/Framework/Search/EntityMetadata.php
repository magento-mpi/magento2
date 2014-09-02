<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search;

/**
 * Entity metadata
 */
class EntityMetadata
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Get entity id
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}

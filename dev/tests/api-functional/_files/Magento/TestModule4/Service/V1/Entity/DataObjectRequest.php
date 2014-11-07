<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

class DataObjectRequest extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->_get('entity_id');
    }
}

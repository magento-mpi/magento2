<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

class NestedDataObjectRequest extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * @return \Magento\TestModule4\Service\V1\Entity\DataObjectRequest
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

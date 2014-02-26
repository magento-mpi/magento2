<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1\Entity;

class NestedDtoRequest extends \Magento\Service\Data\AbstractObject
{
    /**
     * @return \Magento\TestModule4\Service\V1\Entity\DtoRequest
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

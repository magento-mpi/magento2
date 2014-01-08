<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\Entity\V1;

class NestedDtoRequest extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return \Magento\TestModule4\Service\Entity\V1\DtoRequest
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

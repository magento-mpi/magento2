<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\Entity\V1;

class NestedDtoRequestBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param \Magento\TestModule4\Service\Entity\V1\DtoRequest $details
     * @return \Magento\TestModule4\Service\Entity\V1\DtoRequest
     */
    public function setDetails(DtoRequest $details)
    {
        return $this->_set('details', $details);
    }
}

<?php
/**
 * Customer Service Address Interface
 *
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

    /**
     * @param \Magento\TestModule4\Service\Entity\V1\DtoRequest $details
     *
     * @return \Magento\TestModule4\Service\Entity\V1\DtoRequest
     */
    public function setDetails(DtoRequest $details)
    {
        return $this->_set('details', $details);
    }
}

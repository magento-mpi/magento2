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
     * @return DtoRequest
     */
    public function getDetails()
    {
        return $this->_get('details');
    }

    /**
     * @param \Magento\TestModule4\Service\Entity\V1\DtoRequest $details
     *
     * @return NestedDtoRequest
     */
    public function setDetails(DtoRequest $details)
    {
        return $this->_set('details', $details);
    }
}
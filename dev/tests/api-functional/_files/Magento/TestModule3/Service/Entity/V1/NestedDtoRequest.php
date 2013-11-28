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
        return $this->_data['details'];
    }

    /**
     * @param DtoRequest $details
     *
     * @return NestedDtoRequest
     */
    public function setDetails($details)
    {
        $this->_data['details'] = $details;
        return $this;
    }

    protected function _getNestingInfo()
    {
        return ['details' => '\Magento\TestModule4\Service\Entity\V1\DtoRequest'];
    }
}

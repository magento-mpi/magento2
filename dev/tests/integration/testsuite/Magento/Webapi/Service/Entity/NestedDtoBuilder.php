<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class NestedDtoBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto $details
     */
    public function setDetails($details)
    {
        $this->_data['details'] = $details;
    }
}

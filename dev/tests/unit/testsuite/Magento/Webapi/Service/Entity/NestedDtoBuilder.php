<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDtoBuilder;

class NestedDtoBuilder extends AbstractDtoBuilder
{
    /**
     * @param string $details
     * @return $this
     */
    public function setDetails($details)
    {
        $this->_data['details'] = $details;
        return $this;
    }
}

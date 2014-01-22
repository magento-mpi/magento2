<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

class NestedDto extends AbstractDto
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleDto
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

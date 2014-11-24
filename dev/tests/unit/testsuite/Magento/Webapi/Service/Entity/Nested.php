<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\AbstractExtensibleObject;

class Nested extends AbstractExtensibleObject
{
    /**
     * @return \Magento\Webapi\Service\Entity\Simple
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

class NestedData extends AbstractExtensibleObject
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleData
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

class AssociativeArray extends AbstractExtensibleObject
{
    /**
     * @return string[]
     */
    public function getAssociativeArray()
    {
        return $this->_get('associativeArray');
    }
}

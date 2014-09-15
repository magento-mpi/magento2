<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

class SimpleArrayData extends AbstractExtensibleObject
{
    /**
     * @return int[]
     */
    public function getIds()
    {
        return $this->_get('ids');
    }
}

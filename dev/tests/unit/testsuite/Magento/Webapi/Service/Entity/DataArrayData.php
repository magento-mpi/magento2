<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

class DataArrayData extends AbstractExtensibleObject
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleData[]|null
     */
    public function getItems()
    {
        return $this->_get('items');
    }
}

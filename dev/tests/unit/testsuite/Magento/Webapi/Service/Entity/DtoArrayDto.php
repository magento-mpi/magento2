<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

class DtoArrayDto extends AbstractDto
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleDto[]
     */
    public function getItems()
    {
        return $this->_get('items');
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $items
     * @return DtoArrayDto
     */
    public function setItems(array $items)
    {
        return $this->_set('items', $items);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

/**
 * Class DtoArrayDto
 *
 * @package Magento\Webapi\Service\Entity
 */
class DtoArrayDto extends AbstractDto
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleDto[]
     */
    public function getItems()
    {
        return $this->_getData('items');
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $items
     *
     * @return DtoArrayDto
     */
    public function setItems(array $items)
    {
        return $this->_setData('items', $items);
    }
}
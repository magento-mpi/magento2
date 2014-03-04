<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class DtoArrayDtoBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $items
     */
    public function setItems(array $items)
    {
        $this->_data['items'] = $items;
    }
}

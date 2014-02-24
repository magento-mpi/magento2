<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDtoBuilder;

class DtoArrayDtoBuilder extends AbstractDtoBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->_data['items'] = $items;
        return $this;
    }
}

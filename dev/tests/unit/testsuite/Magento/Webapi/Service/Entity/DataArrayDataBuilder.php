<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

class DataArrayDataBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleData[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->_data['items'] = $items;
        return $this;
    }
}

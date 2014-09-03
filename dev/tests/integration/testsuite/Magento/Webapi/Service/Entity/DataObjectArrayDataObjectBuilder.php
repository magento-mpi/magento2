<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class DataObjectArrayDataObjectBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDataObject[] $items
     */
    public function setItems(array $items)
    {
        $this->_data['items'] = $items;
    }
}

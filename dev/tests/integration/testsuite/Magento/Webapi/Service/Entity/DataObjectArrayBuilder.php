<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class DataObjectArrayBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDataObject[] $items
     */
    public function setItems(array $items)
    {
        $this->data['items'] = $items;
    }
}

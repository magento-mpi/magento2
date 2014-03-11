<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\V1\Entity;

class ItemBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param int $itemId
     *
     * @return \Magento\TestModule1\Service\V1\Entity\ItemBuilder
     */
    public function setItemId($itemId)
    {
        $this->_data['item_id'] = $itemId;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Magento\TestModule1\Service\V1\Entity\ItemBuilder
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }
}

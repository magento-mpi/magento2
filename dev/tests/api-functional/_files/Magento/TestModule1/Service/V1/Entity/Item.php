<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\V1\Entity;

class Item extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->_data['item_id'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }
}

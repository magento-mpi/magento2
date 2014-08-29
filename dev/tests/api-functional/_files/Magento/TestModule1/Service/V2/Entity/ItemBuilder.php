<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\V2\Entity;

class ItemBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param int $id
     *
     * @return \Magento\TestModule1\Service\V2\Entity\ItemBuilder
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Magento\TestModule1\Service\V2\Entity\ItemBuilder
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

    /**
     * @param string $price
     *
     * @return \Magento\TestModule1\Service\V2\Entity\ItemBuilder
     */
    public function setPrice($price)
    {
        $this->_data['price'] = $price;
        return $this;
    }
}

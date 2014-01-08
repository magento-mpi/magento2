<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\Entity\V2;

class ItemBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param int $id
     *
     * @return \Magento\TestModule1\Service\Entity\V2\ItemBuilder
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Magento\TestModule1\Service\Entity\V2\ItemBuilder
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

    /**
     * @param string $price
     *
     * @return \Magento\TestModule1\Service\Entity\V2\ItemBuilder
     */
    public function setPrice($price)
    {
        $this->_data['price'] = $price;
        return $this;
    }
}

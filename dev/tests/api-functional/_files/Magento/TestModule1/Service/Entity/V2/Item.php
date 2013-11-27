<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\Entity\V2;


class Item extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->_data['id'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->_data['price'];
    }

    /**
     * @param int $id
     *
     * @return Item
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

    /**
     * @param string $price
     *
     * @return Item
     */
    public function setPrice($price)
    {
        $this->_data['price'] = $price;
        return $this;
    }
}
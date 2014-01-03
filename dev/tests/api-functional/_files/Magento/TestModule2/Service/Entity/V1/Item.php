<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule2\Service\Entity\V1;

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
     * @param int $id
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }
}

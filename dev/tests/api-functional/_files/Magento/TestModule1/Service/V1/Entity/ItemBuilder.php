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
     * @param int $id
     *
     * @return \Magento\TestModule1\Service\V1\Entity\ItemBuilder
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
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

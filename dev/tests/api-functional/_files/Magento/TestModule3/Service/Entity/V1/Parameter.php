<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule3\Service\Entity\V1;

class Parameter extends \Magento\Service\Entity\AbstractDto
{
    /**
     * Set Name.
     *
     * @param string $name
     * @return \Magento\TestModule3\Service\Entity\V1\Parameter
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

    /**
     * Set value.
     *
     * @param string $value
     * @return \Magento\TestModule3\Service\Entity\V1\Parameter
     */
    public function setValue($value)
    {
        $this->_data['value'] = $value;
        return $this;
    }
}

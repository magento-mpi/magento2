<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule3\Service\Entity\V1;


class Parameter extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }

    /**
     * @param string $name
     *
     * @return Parameter
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_data['value'];
    }

    /**
     * @param string $value
     *
     * @return Parameter
     */
    public function setValue($value)
    {
        $this->_data['value'] = $value;
        return $this;
    }


}
<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\Entity\V1;


class DtoRequest extends \Magento\Service\Entity\AbstractDto
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
     * @return DtoRequest
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }

}

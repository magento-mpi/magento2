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
     * Get Name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->_data['name'];
    }

    /**
     * Get value.
     *
     * @return string $value
     */
    public function getValue()
    {
        return $this->_data['value'];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\V1\Entity;

class CustomAttributeNestedDataObject extends \Magento\Framework\Service\Data\Eav\AbstractObject
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }
}

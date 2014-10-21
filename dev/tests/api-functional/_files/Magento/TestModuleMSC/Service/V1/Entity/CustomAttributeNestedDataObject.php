<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1\Entity;

class CustomAttributeNestedDataObject extends \Magento\Framework\Model\AbstractExtensibleModel
    implements CustomAttributeNestedDataObjectInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }
}

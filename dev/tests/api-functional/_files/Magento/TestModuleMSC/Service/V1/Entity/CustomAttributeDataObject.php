<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1\Entity;

class CustomAttributeDataObject extends \Magento\Framework\Model\AbstractExtensibleModel
    implements CustomAttributeDataObjectInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }
}

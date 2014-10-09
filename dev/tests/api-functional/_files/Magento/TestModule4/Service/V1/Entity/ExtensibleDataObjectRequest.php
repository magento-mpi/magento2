<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

class ExtensibleDataObjectRequest extends \Magento\Framework\Model\AbstractExtensibleModel
    implements ExtensibleDataObjectRequestInterface
{
    public function getName()
    {
        return $this->getData("name");
    }
}

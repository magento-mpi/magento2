<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1\Entity;

interface CustomAttributeNestedDataObjectInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getName();
}

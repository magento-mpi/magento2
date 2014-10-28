<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Api\Data;

interface CustomAttributeNestedDataObjectInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getName();
}

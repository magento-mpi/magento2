<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Api\Data;

interface CustomAttributeDataObjectInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    const NAME = 'name';

    /**
     * @return string
     */
    public function getName();
}

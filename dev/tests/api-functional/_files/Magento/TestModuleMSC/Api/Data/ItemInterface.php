<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Api\Data;

interface ItemInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    /**
     * @return int
     */
    public function getItemId();

    /**
     * @return string
     */
    public function getName();
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1\Entity;

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

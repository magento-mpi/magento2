<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

interface ExtensibleDataObjectRequestInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return int|null
     */
    public function getEntityId();
}

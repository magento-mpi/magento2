<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data\Eav;

interface MetadataServiceInterface
{
    /**
     *  Get custom attribute metadata for the given Data object's attribute set
     *
     * @return \Magento\Framework\Service\Data\Eav\MetadataObjectInterface[]
     */
    public function getCustomAttributesMetadata();
}

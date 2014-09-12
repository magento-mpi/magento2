<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

interface MetadataServiceInterface
{
    /**
*  Get custom attribute metadata for the given Data object's attribute set
     *
     * @param string|null $dataObjectClassName Data object class name
     * @return \Magento\Framework\Service\Data\MetadataObjectInterface[]
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null);
}

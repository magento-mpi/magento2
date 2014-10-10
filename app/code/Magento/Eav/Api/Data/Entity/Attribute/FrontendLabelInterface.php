<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data\Entity\Attribute;

interface FrontendLabelInterface
{
    /**
     * Get store id value
     *
     * @return string
     */
    public function getStoreId();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();
}

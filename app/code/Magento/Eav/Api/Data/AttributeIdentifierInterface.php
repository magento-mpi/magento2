<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeIdentifierInterface
{
    /**
     * Return entity type code
     *
     * @return string
     */
    public function getEntityTypeCode();

    /**
     * Return attribute code
     *
     * @return string
     */
    public function getAttributeCode();
}

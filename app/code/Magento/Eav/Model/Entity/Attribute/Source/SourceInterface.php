<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity attribute select source interface
 *
 * Source is providing the selection options for user interface
 *
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

interface SourceInterface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions();

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value);
}

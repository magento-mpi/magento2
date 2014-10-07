<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * Interface AttributeSet must be implemented by \Magento\Eav\Model\Entity\Attribute\Set
 */
interface AttributeSetInterface
{
    /**
     * Get attribute set id
     *
     * @return int
     */
    public function getId();

    /**
     * Get attribute set name
     *
     * @return string
     */
    public function getName();

    /**
     * Get attribute set sort order index
     *
     * @return int
     */
    public function getSortOrder();
}

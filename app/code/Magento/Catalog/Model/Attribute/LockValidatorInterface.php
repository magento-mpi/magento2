<?php
/**
 * Attribure lock state validator interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute;

interface LockValidatorInterface
{
    /**
     * Check attribute lock state
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Framework\Model\Exception
     *
     * @return void
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object, $attributeSet = null);
}

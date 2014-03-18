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
     * @param \Magento\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Model\Exception
     *
     * @return void
     */
    public function validate(\Magento\Model\AbstractModel $object, $attributeSet = null);
} 

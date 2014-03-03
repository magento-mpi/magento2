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
     * @param \Magento\Core\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Core\Exception
     *
     * @return void
     */
    public function validate(\Magento\Core\Model\AbstractModel $object, $attributeSet = null);
} 

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

interface ProductAttributeSetReadServiceInterface
{
    /**
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet[]
     */
    public function getList();
}

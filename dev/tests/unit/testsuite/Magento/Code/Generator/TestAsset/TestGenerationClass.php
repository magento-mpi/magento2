<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator\TestAsset;

class TestGenerationClass
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Code\Generator\TestAsset\ParentClass $parentClass
     * @param \Magento\Code\Generator\TestAsset\SourceClass $sourceClass
     * @param \Not_Existing_Class $notExistingClass
     */
    public function __construct(
        \Magento\Code\Generator\TestAsset\ParentClass $parentClass,
        \Magento\Code\Generator\TestAsset\SourceClass $sourceClass,
        \Not_Existing_Class $notExistingClass
    ) {
    }
}

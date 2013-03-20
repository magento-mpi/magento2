<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_OneOptionalArgument
{
    /**
     * @var Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_NoConstructor
     */
    protected $_varA;

    public function __construct(Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_NoConstructor $varA = null)
    {
        $this->_varA = $varA;
    }
}

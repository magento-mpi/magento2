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

class Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_TwoOptionalArguments
{
    /**
     * @var int
     */
    protected $_varA;

    /**
     * @var Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_OneOptionalArgument
     */
    protected $_varB;

    public function __construct($varA = 1,
        Magento_ObjectManager_Definition_CompilerDefinition_TestAsset_OneOptionalArgument $varB = null
    ) {
        $this->_varA = $varA;
        $this->_varB = $varB;
    }
}

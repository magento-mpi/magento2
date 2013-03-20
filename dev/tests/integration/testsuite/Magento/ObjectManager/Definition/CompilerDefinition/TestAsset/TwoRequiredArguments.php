<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Definition_CompilerDefinition_TestAsset_TwoRequiredArguments
{
    /**
     * @var Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor
     */
    protected $_varA;

    /**
     * @var Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument
     */
    protected $_varB;

    public function __construct(Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor $varA,
        Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredArgument $varB
    ) {
        $this->_varA = $varA;
        $this->_varB = $varB;
    }
}

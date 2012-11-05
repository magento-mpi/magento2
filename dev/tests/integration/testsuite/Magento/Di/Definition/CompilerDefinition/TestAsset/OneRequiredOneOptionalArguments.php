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

class Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredOneOptionalArguments
{
    /**
     * @var int
     */
    protected $_varA;

    /**
     * @var Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument
     */
    protected $_varB;

    public function __construct($varA,
        Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument $varB = null
    ) {
        $this->_varA = $varA;
        $this->_varB = $varB;
    }
}

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

class Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredArgument
{
    /**
     * @var Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor
     */
    protected $_varA;

    public function __construct(Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor $varA)
    {
        $this->_varA = $varA;
    }
}

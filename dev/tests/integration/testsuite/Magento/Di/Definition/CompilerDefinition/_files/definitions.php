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

return array(
    'Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor'                   =>
    array(
        'supertypes'   =>
        array(),
        'instantiator' => '__construct',
        'methods'      =>
        array(),
        'parameters'   =>
        array(),
    ),
    'Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument'             =>
    array(
        'supertypes'   =>
        array(),
        'instantiator' => '__construct',
        'methods'      =>
        array(
            '__construct' => true,
        ),
        'parameters'   =>
        array(
            '__construct' =>
            array(
                'Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument::__construct:0' =>
                array(
                    0 => 'varA',
                    1 => 'Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor',
                    2 => false,
                    3 => NULL,
                ),
            ),
        ),
    ),
    'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredArgument'             =>
    array(
        'supertypes'   =>
        array(),
        'instantiator' => '__construct',
        'methods'      =>
        array(
            '__construct' => true,
        ),
        'parameters'   =>
        array(
            '__construct' =>
            array(
                'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredArgument::__construct:0' =>
                array(
                    0 => 'varA',
                    1 => 'Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor',
                    2 => true,
                    3 => NULL,
                ),
            ),
        ),
    ),
    'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredOneOptionalArguments' =>
    array(
        'supertypes'   =>
        array(),
        'instantiator' => '__construct',
        'methods'      =>
        array(
            '__construct' => true,
        ),
        'parameters'   =>
        array(
            '__construct' =>
            array(
                'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredOneOptionalArguments::__construct:0' =>
                array(
                    0 => 'varA',
                    1 => NULL,
                    2 => false,
                    3 => 1,
                ),
                'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredOneOptionalArguments::__construct:1' =>
                array(
                    0 => 'varB',
                    1 => 'Magento_Di_Definition_CompilerDefinition_TestAsset_OneOptionalArgument',
                    2 => false,
                    3 => NULL,
                ),
            ),
        ),
    ),
    'Magento_Di_Definition_CompilerDefinition_TestAsset_TwoRequiredArguments'            =>
    array(
        'supertypes'   =>
        array(),
        'instantiator' => '__construct',
        'methods'      =>
        array(
            '__construct' => true,
        ),
        'parameters'   =>
        array(
            '__construct' =>
            array(
                'Magento_Di_Definition_CompilerDefinition_TestAsset_TwoRequiredArguments::__construct:0' =>
                array(
                    0 => 'varA',
                    1 => 'Magento_Di_Definition_CompilerDefinition_TestAsset_NoConstructor',
                    2 => true,
                    3 => NULL,
                ),
                'Magento_Di_Definition_CompilerDefinition_TestAsset_TwoRequiredArguments::__construct:1' =>
                array(
                    0 => 'varB',
                    1 => 'Magento_Di_Definition_CompilerDefinition_TestAsset_OneRequiredArgument',
                    2 => true,
                    3 => NULL,
                ),
            ),
        ),
    ),
);

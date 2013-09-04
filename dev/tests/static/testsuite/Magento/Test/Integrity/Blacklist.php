<?php
/**
 * Files excluded from the integrity test for PSR-X standards
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

$webapiDir = '/dev/tests/integration/testsuite/Magento/Webapi/';
return array(
    '/app/code/Zend/Soap/Wsdl.php',
    '/dev/tests/unit/testsuite/Magento/Test/Tools/Di/_files/app/code/Magento/SomeModule/Model/Test.php',
    '/dev/tests/unit/testsuite/Magento/Test/Tools/Di/_files/app/code/Magento/SomeModule/Helper/Test.php',
    $webapiDir.'_files/autodiscovery/resource_class_fixture.php',
    $webapiDir.'_files/autodiscovery/subresource_class_fixture.php',
    $webapiDir.'_files/Controller/AutoDiscover/ModuleB.php',
    $webapiDir.'_files/Controller/Webapi/ModuleA.php',
    $webapiDir.'_files/Controller/Webapi/SubresourceB.php',
    $webapiDir.'_files/data_types/CustomerData.php',
    $webapiDir.'_files/data_types/Customer/AddressData.php',
    $webapiDir.'_files/Model/Webapi/ModuleA/ModuleAData.php',
    $webapiDir.'_files/Model/Webapi/ModuleA/ModuleADataB.php',
    $webapiDir.'_files/Model/Webapi/ModuleB/ModuleBData.php',
    $webapiDir.'_files/Model/Webapi/ModuleB/Subresource/SubresourceData.php',
    $webapiDir.'Model/_files/resource_with_invalid_interface.php',
    $webapiDir.'Model/_files/resource_with_invalid_name.php',
    $webapiDir.'Model/_files/autodiscovery/empty_property_description/class.php',
    $webapiDir.'Model/_files/autodiscovery/empty_property_description/data_type.php',
    $webapiDir.'Model/_files/autodiscovery/empty_var_tags/class.php',
    $webapiDir.'Model/_files/autodiscovery/empty_var_tags/data_type.php',
    $webapiDir.'Model/_files/autodiscovery/invalid_deprecation_policy/class.php',
    $webapiDir.'Model/_files/autodiscovery/no_resources/class.php',
    $webapiDir.'Model/_files/autodiscovery/reference_to_invalid_type/class.php',
    $webapiDir.'Model/_files/autodiscovery/several_classes_in_one_file/file_with_classes.php',
);


<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that provides the test suite that includes only tests relevant to the current set of enabled modules
 */
class AllRelevantTests
{
    public static function suite()
    {
        $fileIteratorFactory = new File_Iterator_Factory();
        $suite = new Magento_Test_TestSuite_ModuleGroups(false);
        $suite->addTestFiles(
            $fileIteratorFactory->getFileIterator(
                array(__DIR__),
                array('Test.php')
            )
        );
        return $suite;
    }
}

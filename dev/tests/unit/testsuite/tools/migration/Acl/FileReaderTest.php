<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/tools/migration/Acl/FileReader.php';

class Tools_Migration_Acl_FileReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $filePath
     * @param $expectedMap
     *
     * @dataProvider getAclIdentifiersMapDataProvider
     */
    public function testGetAclIdentifiersMap($filePath, $expectedMap)
    {
        $model = new Tools_Migration_Acl_FileReader(array('filePath' => $filePath));

        $this->assertEquals($expectedMap, $model->getAclIdentifiersMap());
    }

    public function getAclIdentifiersMapDataProvider()
    {
        return array(
            array(
                'filePath' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'log'
                    . DIRECTORY_SEPARATOR . 'AclXPathToAclId.log',
                'expected' => array(
                    "config/acl/resources/admin/test1/test2"        => "Test1_Test2::all",
                    "config/acl/resources/admin/test1/test2/test3"  => "Test1_Test2::test3",
                    "config/acl/resources/admin/test1/test2/test4"  => "Test1_Test2::test4",
                    "config/acl/resources/admin/test1/test2/test5"  => "Test1_Test2::test5",
                    "config/acl/resources/admin/test6"              => "Test6_Test6::all"
                )
            ),
            array(
                'filePath' => __DIR__ . DIRECTORY_SEPARATOR . 'pathToUnspecifiedFile.log',
                'expected' => array(),
            ),
        );
    }
}


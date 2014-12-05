<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;


class DbVersionDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbVersionDetector
     */
    private $dbVersionDetector;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Module\ResourceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleResource;

    protected function setUp()
    {
        $this->moduleList = $this->getMockForAbstractClass('Magento\Framework\Module\ModuleListInterface');
        $this->moduleList->expects($this->any())
            ->method('getModule')
            ->will($this->returnValueMap([
                        ['Module_One', ['name' => 'One_Module', 'schema_version' => '1']],
                        ['Module_Two', ['name' => 'Two_Module', 'schema_version' => '2']],
                        ['Module_Three', ['name' => 'Two_Three']],
                    ]));
        $this->_outputConfig = $this->getMockForAbstractClass('Magento\Framework\Module\Output\ConfigInterface');
        $this->moduleResource = $this->getMockForAbstractClass('\Magento\Framework\Module\ResourceInterface');
        $this->dbVersionDetector = new DbVersionDetector(
            $this->moduleList,
            $this->moduleResource
        );
    }

    /**
     * @param string $moduleName
     * @param string|bool $dbVersion
     * @param bool $expectedResult
     *
     * @dataProvider isDbUpToDateDataProvider
     */
    public function testIsDbSchemaUpToDate($moduleName, $dbVersion, $expectedResult)
    {
        $resourceName = 'resource';
        $this->moduleResource->expects($this->once())
            ->method('getDbVersion')
            ->with($resourceName)
            ->will($this->returnValue($dbVersion));
        $this->assertEquals(
            $expectedResult,
            $this->dbVersionDetector->isDbSchemaUpToDate($moduleName, $resourceName)
        );
    }

    /**
     * @param string $moduleName
     * @param string|bool $dbVersion
     * @param bool $expectedResult
     *
     * @dataProvider isDbUpToDateDataProvider
     */
    public function testIsDbDataUpToDate($moduleName, $dbVersion, $expectedResult)
    {
        $resourceName = 'resource';
        $this->moduleResource->expects($this->once())
            ->method('getDataVersion')
            ->with($resourceName)
            ->will($this->returnValue($dbVersion));
        $this->assertEquals(
            $expectedResult,
            $this->dbVersionDetector->isDbDataUpToDate($moduleName, $resourceName)
        );
    }


    /**
     * @return array
     */
    public function isDbUpToDateDataProvider()
    {
        return [
            'version in config == version in db' => ['Module_One', '1', true],
            'version in config < version in db' =>
                [
                    'Module_One',
                    '2',
                    false
                ],
            'version in config > version in db' =>
                [
                    'Module_Two',
                    '1',
                    false
                ],
            'no version in db' =>
                [
                    'Module_One',
                    false,
                    false
                ],
        ];
    }

    public function testGetDbVersionErrors()
    {
        $this->moduleList->expects($this->any())
            ->method('getModules')
            ->will($this->returnValue([
                        ['name' => 'One_Module', 'schema_version' => '1'],
                        ['Module_Two', ['name' => 'Two_Module', 'schema_version' => '2']],
                        ['Module_Three', ['name' => 'Two_Three']],
                    ]));

        $this->dbVersionDetector->getDbVersionErrors();
    }

//
//    /**
//     * @expectedException \UnexpectedValueException
//     * @expectedExceptionMessage Schema version for module 'Module_Three' is not specified
//     */
//    public function testIsDbSchemaUpToDateException()
//    {
//        $this->dbVersionDetector->getDbSchemaVersionError('Module_Three', 'resource');
//    }
//
//    /**
//     * @expectedException \UnexpectedValueException
//     * @expectedExceptionMessage Schema version for module 'Module_Three' is not specified
//     */
//    public function testIsDbDataUpToDateException()
//    {
//        $this->dbVersionDetector->getDbDataVersionError('Module_Three', 'resource');
//    }
}

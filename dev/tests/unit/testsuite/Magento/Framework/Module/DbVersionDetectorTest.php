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

    /**
     * @var Magento\Framework\Module\ResourceResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceResolver;

    protected function setUp()
    {
        $this->moduleList = $this->getMockForAbstractClass('Magento\Framework\Module\ModuleListInterface');
        $this->moduleList->expects($this->any())
            ->method('getOne')
            ->will($this->returnValueMap([
                        ['Module_One', ['name' => 'Module_One', 'schema_version' => '1']],
                        ['Module_Two', ['name' => 'Module_Two', 'schema_version' => '2']],
                        ['Module_No_Schema', []],
                    ]));
        $this->moduleList->expects($this->any())
            ->method('getNames')
            ->will($this->returnValue(['Module_One', 'Module_Two']));

        $this->_outputConfig = $this->getMockForAbstractClass('Magento\Framework\Module\Output\ConfigInterface');
        $this->moduleResource = $this->getMockForAbstractClass('\Magento\Framework\Module\ResourceInterface');
        $this->resourceResolver = $this->getMockForAbstractClass('\Magento\Framework\Module\ResourceResolverInterface');

        $this->dbVersionDetector = new DbVersionDetector(
            $this->moduleList,
            $this->moduleResource,
            $this->resourceResolver
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
        $this->moduleResource->expects($this->any())
            ->method('getDataVersion')
            ->will($this->returnValue(2));
        $this->moduleResource->expects($this->any())
            ->method('getDbVersion')
            ->will($this->returnValue(2));

        $this->resourceResolver->expects($this->any())->method('getResourceList')->will($this->returnValueMap([
                    ['Module_One', ['resource_one']],
                    ['Module_Two', ['resource_two']],
                ]));

        $expectedErrors = [
            [
                DbVersionDetector::ERROR_KEY_MODULE => 'Module_One',
                DbVersionDetector::ERROR_KEY_CURRENT => '2',
                DbVersionDetector::ERROR_KEY_REQUIRED => '1',
                DbVersionDetector::ERROR_KEY_TYPE => 'schema',
            ],
            [
                DbVersionDetector::ERROR_KEY_MODULE => 'Module_One',
                DbVersionDetector::ERROR_KEY_CURRENT => '2',
                DbVersionDetector::ERROR_KEY_REQUIRED => '1',
                DbVersionDetector::ERROR_KEY_TYPE => 'data',
            ]
        ];
        $this->assertEquals($expectedErrors, $this->dbVersionDetector->getDbVersionErrors());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Schema version for module 'Module_No_Schema' is not specified
     */
    public function testIsDbSchemaUpToDateException()
    {
        $this->dbVersionDetector->isDbSchemaUpToDate('Module_No_Schema', 'resource_name');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Schema version for module 'Module_No_Schema' is not specified
     */
    public function testIsDbDataUpToDateException()
    {
        $this->dbVersionDetector->isDbDataUpToDate('Module_No_Schema', 'resource_name');
    }
}

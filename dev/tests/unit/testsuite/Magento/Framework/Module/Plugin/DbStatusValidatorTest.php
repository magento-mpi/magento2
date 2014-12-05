<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Plugin;

use Magento\Framework\Module\DbVersionDetector;

class DbStatusValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Module\Plugin\DbStatusValidator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbUpdaterMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Module\DbVersionDetector|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dbVersionDetectorMock;

    protected function setUp()
    {
        $this->_cacheMock = $this->getMock('\Magento\Framework\Cache\FrontendInterface');
        $this->_dbUpdaterMock = $this->getMock('\Magento\Framework\Module\Updater', [], [], '', false);
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->subjectMock = $this->getMock('Magento\Framework\App\FrontController', array(), array(), '', false);
        $moduleList = $this->getMockForAbstractClass('\Magento\Framework\Module\ModuleListInterface');
        $moduleList->expects($this->any())
            ->method('getNames')
            ->will($this->returnValue(['Module_One', 'Module_Two']));
        $resourceResolver = $this->getMockForAbstractClass('\Magento\Framework\Module\ResourceResolverInterface');
        $resourceResolver->expects($this->any())
            ->method('getResourceList')
            ->will($this->returnCallback(function ($moduleName) {
                return ['resource_' . $moduleName];
            }));
        $this->moduleManager = $this->getMock('\Magento\Framework\Module\Manager', [], [], '', false);
        $this->dbVersionDetectorMock = $this->getMock('\Magento\Framework\Module\DbVersionDetector', [], [], '', false);
        $this->_model = new DbStatusValidator(
            $this->_cacheMock,
            $this->dbVersionDetectorMock
        );
    }

    public function testAroundDispatch()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('db_is_up_to_date')
            ->will($this->returnValue(false))
        ;
        $returnMap = [
            ['Module_One', 'resource_Module_One', true],
            ['Module_Two', 'resource_Module_Two', true],
        ];
        $this->moduleManager->expects($this->any())
            ->method('isDbSchemaUpToDate')
            ->will($this->returnValueMap($returnMap));
        $this->moduleManager->expects($this->any())
            ->method('isDbDataUpToDate')
            ->will($this->returnValueMap($returnMap));

        $this->assertEquals(
            'Expected',
            $this->_model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchCached()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('db_is_up_to_date')
            ->will($this->returnValue(true))
        ;
        $this->moduleManager->expects($this->never())
            ->method('isDbSchemaUpToDate');
        $this->moduleManager->expects($this->never())
            ->method('isDbDataUpToDate');
        $this->assertEquals(
            'Expected',
            $this->_model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    /**
     * @param array $dbVersionErrors
     *
     * @dataProvider aroundDispatchExceptionDataProvider
     * @expectedException \Magento\Framework\Module\Exception
     * @expectedExceptionMessage Please update your database: first run "composer install" from the Magento
     */
    public function testAroundDispatchException(array $dbVersionErrors)
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('db_is_up_to_date')
            ->will($this->returnValue(false))
        ;
        $this->_cacheMock->expects($this->never())->method('save');

        $this->dbVersionDetectorMock->expects($this->any())
            ->method('getDbVersionErrors')
            ->will($this->returnValue($dbVersionErrors));

        $this->_model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock);
    }

    /**
     * @return array
     */
    public function aroundDispatchExceptionDataProvider()
    {
        return [
            'schema is outdated' => [
                [
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_One',
                         DbVersionDetector::ERROR_KEY_TYPE => 'schema',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ]
                ],
            ],
            'data is outdated' => [
                [
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_Two',
                         DbVersionDetector::ERROR_KEY_TYPE => 'data',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ]
                ],
            ],
            'both schema and data are outdated' => [
                [
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_One',
                         DbVersionDetector::ERROR_KEY_TYPE => 'schema',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ],
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_Two',
                         DbVersionDetector::ERROR_KEY_TYPE => 'schema',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ],
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_One',
                         DbVersionDetector::ERROR_KEY_TYPE => 'data',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ],
                     [
                         DbVersionDetector::ERROR_KEY_MODULE => 'Module_Two',
                         DbVersionDetector::ERROR_KEY_TYPE => 'data',
                         DbVersionDetector::ERROR_KEY_CURRENT => 'none',
                         DbVersionDetector::ERROR_KEY_NEEDED => '1'
                     ]
                ],
            ],
        ];
    }
}

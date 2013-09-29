<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Module;

class ResourceResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Magento\Core\Model\Module\ResourceResolver
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock('Magento\Core\Model\Config\Modules\Reader',
            array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Module\ResourceResolver($this->_moduleReaderMock);
    }

    public function testGetResourceList()
    {
        $moduleName = 'Module';
        $this->_moduleReaderMock->expects($this->any())
            ->method('getModuleDir')
            ->will($this->returnValueMap(array(
                array(
                    'data',
                    $moduleName,
                    __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/ResourceResolver/_files/Module/data'),
                ),
                array(
                    'sql',
                    $moduleName,
                    __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/ResourceResolver/_files/Module/sql'),
                ),
            )));

        $expectedResult = array('module_first_setup', 'module_second_setup');
        $this->assertEquals($expectedResult, array_values($this->_model->getResourceList($moduleName)));
    }
}

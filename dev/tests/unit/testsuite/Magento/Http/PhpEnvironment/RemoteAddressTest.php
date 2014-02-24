<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Http\PhpEnvironment;

class RemoteAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;


    protected function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getServer'))
            ->getMock();

        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

    }

    /**
     * @dataProvider getRemoteAddressProvider
     */
    public function testGetRemoteAddress($alternativeHeaders, $serverValueMap, $expected, $ipToLong)
    {
        $remoteAddress = $this->_objectManager->getObject('Magento\HTTP\PhpEnvironment\RemoteAddress', array(
            'httpRequest' => $this->_request,
            'alternativeHeaders' => $alternativeHeaders
        ));
        $this->_request->expects($this->any())
            ->method('getServer')
            ->will($this->returnValueMap($serverValueMap));
        $this->assertEquals($expected, $remoteAddress->getRemoteAddress($ipToLong));
    }

    /**
     * @return array
     */
    public function getRemoteAddressProvider()
    {
        return array(
            array(
                'alternativeHeaders' => array(),
                'serverValueMap' => array(array('REMOTE_ADDR', null, null)),
                'expected' => false,
                'ipToLong' => false
            ),
            array(
                'alternativeHeaders' => array(),
                'serverValueMap' => array(array('REMOTE_ADDR', null, '192.168.0.1')),
                'expected' => '192.168.0.1',
                'ipToLong' => false
            ),
            array(
                'alternativeHeaders' => array(),
                'serverValueMap' => array(array('REMOTE_ADDR', null, '192.168.1.1')),
                'expected' => ip2long('192.168.1.1'),
                'ipToLong' => true
            ),
            array(
                'alternativeHeaders' => array('TEST_HEADER'),
                'serverValueMap' => array(
                    array('REMOTE_ADDR', null, '192.168.1.1'),
                    array('TEST_HEADER', null, '192.168.0.1'),
                    array('TEST_HEADER', false, '192.168.0.1')
                ),
                'expected' => '192.168.0.1',
                'ipToLong' => false
            ),
            array(
                'alternativeHeaders' => array('TEST_HEADER'),
                'serverValueMap' => array(
                    array('REMOTE_ADDR', null, '192.168.1.1'),
                    array('TEST_HEADER', null, '192.168.0.1'),
                    array('TEST_HEADER', false, '192.168.0.1')
                ),
                'expected' => ip2long('192.168.0.1'),
                'ipToLong' => true
            )
        );
    }
}

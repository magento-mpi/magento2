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

/**
 * Test class for \Magento\UrlInterface
 */
namespace Magento\Core\Model;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $paramsResolverMock = $this->getMock(
            'Magento\Url\RouteParamsResolverFactory', array(), array(), '', false
        );
        $paramsResolver = $this->_objectManager->getObject('\Magento\Core\Model\Url\RouteParamsResolver');
        $paramsResolverMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($paramsResolver));
        $this->_model = $this->_objectManager->getObject(
            '\Magento\Url', array('routeParamsResolver' => $paramsResolverMock)
        );
    }
    /**
     * @param $port mixed
     * @param $url string
     * @dataProvider getCurrentUrlProvider
     */
    public function testGetCurrentUrl($port, $url)
    {
        $methods = array('getServer', 'getScheme', 'getHttpHost', 'getModuleName', 'setModuleName',
            'getActionName', 'setActionName', 'getParam');
        $requestMock = $this->getMock('\Magento\App\RequestInterface', $methods);
        $requestMock->expects($this->at(0))->method('getServer')->with('SERVER_PORT')
            ->will($this->returnValue($port));
        $requestMock->expects($this->at(1))->method('getServer')->with('REQUEST_URI')
            ->will($this->returnValue('/fancy_uri'));
        $requestMock->expects($this->once())->method('getScheme')->will($this->returnValue('http'));
        $requestMock->expects($this->once())->method('getHttpHost')->will($this->returnValue('example.com'));

        /** @var \Magento\UrlInterface $model */
        $model = $this->_objectManager->getObject('Magento\Url', array('request' => $requestMock));
        $this->assertEquals($url, $model->getCurrentUrl());
    }

    public function getCurrentUrlProvider()
    {
        return array(
            'without_port' => array('', 'http://example.com/fancy_uri'),
            'default_port' => array(80, 'http://example.com/fancy_uri'),
            'custom_port' => array(8080, 'http://example.com:8080/fancy_uri')
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Config\Processor;

class PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\Config\Processor\Placeholder
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_requestMock->expects($this->any())
            ->method('getDistroBaseUrl')
            ->will($this->returnValue('http://localhost/'));
        $this->_model = new \Magento\Store\Model\Config\Processor\Placeholder(
            $this->_requestMock,
            array(
                'unsecureBaseUrl' => \Magento\Store\Model\Store::XML_PATH_UNSECURE_BASE_URL,
                'secureBaseUrl' => \Magento\Store\Model\Store::XML_PATH_SECURE_BASE_URL
            ),
            \Magento\Store\Model\Store::BASE_URL_PLACEHOLDER
        );
    }

    public function testProcess()
    {
        $data = array(
            'web' => array(
                'unsecure' => array(
                    'base_url' => 'http://localhost/',
                    'base_link_url' => '{{unsecure_base_url}}website/de',
                ),
                'secure' => array(
                    'base_url' => 'https://localhost/',
                    'base_link_url' => '{{secure_base_url}}website/de',
                ),
            ),
            'path' => 'value',
            'some_url' => '{{base_url}}some'
        );
        $expectedResult = $data;
        $expectedResult['web']['unsecure']['base_link_url'] = 'http://localhost/website/de';
        $expectedResult['web']['secure']['base_link_url'] = 'https://localhost/website/de';
        $expectedResult['some_url'] = 'http://localhost/some';
        $this->assertEquals($expectedResult, $this->_model->process($data));
    }
}

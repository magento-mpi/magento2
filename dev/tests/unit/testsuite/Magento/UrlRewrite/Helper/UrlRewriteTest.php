<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Helper;

use Magento\TestFramework\Helper\ObjectManager;

class UrlRewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Helper\UrlRewrite
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = (new ObjectManager($this))->getObject('Magento\UrlRewrite\Helper\UrlRewrite');
    }

    /**
     * @dataProvider requestPathDataProvider
     */
    public function testValidateRequestPath($requestPath)
    {
        $this->assertTrue($this->_helper->validateRequestPath($requestPath));
    }

    /**
     * @dataProvider requestPathExceptionDataProvider
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testValidateRequestPathException($requestPath)
    {
        $this->_helper->validateRequestPath($requestPath);
    }

    /**
     * @dataProvider requestPathDataProvider
     */
    public function testValidateSuffix($suffix)
    {
        $this->assertTrue($this->_helper->validateSuffix($suffix));
    }

    /**
     * @dataProvider requestPathExceptionDataProvider
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testValidateSuffixException($suffix)
    {
        $this->_helper->validateSuffix($suffix);
    }

    public function requestPathDataProvider()
    {
        return array(
            'no leading slash' => array('correct/request/path'),
            'leading slash' => array('another/good/request/path/')
        );
    }

    public function requestPathExceptionDataProvider()
    {
        return array(
            'two slashes' => array('request/path/with/two//slashes'),
            'three slashes' => array('request/path/with/three///slashes'),
            'anchor' => array('request/path/with#anchor')
        );
    }
}

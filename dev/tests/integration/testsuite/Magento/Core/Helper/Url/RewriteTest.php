<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Helper_Url_RewriteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Helper_Url_Rewrite
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Helper_Url_Rewrite');
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
     * @expectedException Magento_Core_Exception
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
     * @expectedException Magento_Core_Exception
     */
    public function testValidateSuffixException($suffix)
    {
        $this->_helper->validateSuffix($suffix);
    }

    public function requestPathDataProvider()
    {
        return array(
            'no leading slash' => array('correct/request/path'),
            'leading slash'    => array('another/good/request/path/'),
        );
    }

    public function requestPathExceptionDataProvider()
    {
        return array(
            'two slashes'   => array('request/path/with/two//slashes'),
            'three slashes' => array('request/path/with/three///slashes'),
            'anchor'        => array('request/path/with#anchor'),
        );
    }
}

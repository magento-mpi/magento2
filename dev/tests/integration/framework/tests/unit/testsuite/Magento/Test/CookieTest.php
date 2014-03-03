<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Name of the sample cookie to be used in tests
     */
    const SAMPLE_COOKIE_NAME = 'sample_cookie';

    /**
     * @var \Magento\TestFramework\Cookie
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\TestFramework\Cookie(
            new \Magento\TestFramework\Request(
                $this->getMock('\Magento\App\Route\ConfigInterface'),
                $this->getMock('Magento\App\Request\PathInfoProcessorInterface'),
                'http://example.com'
            ),
            new \Magento\TestFramework\Response(
                $this->getMock('\Magento\Stdlib\Cookie', array(), array(), '', false),
                $this->getMock('Magento\App\Http\Context', array(), array(), '', false)
            )
        );
    }

    public function testSet()
    {
        $cookieValue = 'some_cookie_value';
        $this->assertFalse($this->_model->get(self::SAMPLE_COOKIE_NAME));
        $this->_model->set(self::SAMPLE_COOKIE_NAME, $cookieValue);
        $this->assertEquals($cookieValue, $this->_model->get(self::SAMPLE_COOKIE_NAME));
        $this->assertEquals($cookieValue, $_COOKIE[self::SAMPLE_COOKIE_NAME]);
    }
}

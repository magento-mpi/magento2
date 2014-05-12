<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\PageCache;

/**
 * Class FormKeyTest
 */
class FormKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var FormKey
     */
    protected $formKey;

    /**
     * Cookie mock
     *
     * @var \Magento\Framework\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMock;

    /**
     * Create cookie mock and FormKey instance
     */
    public function setUp()
    {
        $this->cookieMock = $this->getMock('Magento\Framework\Stdlib\Cookie', array('get'), array(), '', false);
        $this->formKey =  new \Magento\Framework\App\PageCache\FormKey($this->cookieMock);
    }

    public function testGet()
    {
        //Data
        $formKey = 'test from key';

        //Verification
        $this->cookieMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Framework\App\PageCache\FormKey::COOKIE_NAME)
            ->will($this->returnValue($formKey));

        $this->assertEquals($formKey, $this->formKey->get());
    }
}

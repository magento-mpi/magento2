<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\PageCache;

/**
 * Class FormKeyTest
 * @package Magento\App\PageCache
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
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMock;

    /**
     * Create cookie mock and FormKey instance
     */
    public function setUp()
    {
        $this->cookieMock = $this->getMock('Magento\Stdlib\Cookie', array('get'), array(), '', false);
        $this->formKey =  new FormKey($this->cookieMock);
    }

    public function testGet()
    {
        //Data
        $formKey = 'test from key';

        //Verification
        $this->cookieMock->expects($this->once())
            ->method('get')
            ->with(FormKey::COOKIE_NAME)
            ->will($this->returnValue($formKey));

        $this->assertEquals($formKey, $this->formKey->get());
    }
}

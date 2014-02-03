<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Response;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Response\Http
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\App\Response\Http();
        $this->_model->headersSentThrowsException = false;
        $this->_model->setHeader('name', 'value');
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetHeaderWhenHeaderNameIsEqualsName()
    {
        $expected = array('name' => 'Name', 'value' => 'value', 'replace' => false);
        $actual = $this->_model->getHeader('Name');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHeaderWhenHeaderNameIsNotEqualsName()
    {
        $this->assertFalse($this->_model->getHeader('Test'));
    }

    public function testGetVaryString()
    {
        $vary = array('some-vary-key' => 'some-vary-value');
        ksort($vary);
        $expected = sha1(serialize($vary));
        $this->_model->setVary('some-vary-key', 'some-vary-value');

        $this->assertEquals($expected, $this->_model->getVaryString());
    }
}

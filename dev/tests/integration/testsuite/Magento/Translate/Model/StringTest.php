<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translate\Model;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Translate\Model\String
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Translate\Model\String');
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Magento\Translate\Model\Resource\String', $this->_model->getResource());
    }

    public function testSetGetString()
    {
        $expectedString = __METHOD__;
        $this->_model->setString($expectedString);
        $actualString = $this->_model->getString();
        $this->assertEquals($expectedString, $actualString);
    }
}

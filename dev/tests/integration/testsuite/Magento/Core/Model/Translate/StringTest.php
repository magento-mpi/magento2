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

class Magento_Core_Model_Translate_StringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Translate\String
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\Core\Model\Translate\String');
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Magento\Core\Model\Resource\Translate\String', $this->_model->getResource());
    }

    public function testSetGetString()
    {
        $expectedString = __METHOD__;
        $this->_model->setString($expectedString);
        $actualString = $this->_model->getString();
        $this->assertEquals($expectedString, $actualString);
    }
}

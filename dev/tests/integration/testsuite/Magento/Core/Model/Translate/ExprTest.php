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

class Magento_Core_Model_Translate_ExprTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Translate_Expr
     */
    protected $_model;

    protected $_expectedText   = __FILE__;
    protected $_expectedModule = __CLASS__;

    public function setUp()
    {
        $this->_model = Mage::getModel(
            'Magento_Core_Model_Translate_Expr',
            array('text' => $this->_expectedText, 'module' => $this->_expectedModule)
        );
    }

    public function testConstructor()
    {
        $this->assertEquals($this->_expectedText, $this->_model->getText());
        $this->assertEquals($this->_expectedModule, $this->_model->getModule());
    }

    public function testSetTextSetModule()
    {
        $expectedText = __FILE__ . '!!!';
        $expectedModule = __CLASS__ . '!!!';
        $this->_model->setText($expectedText);
        $this->_model->setModule($expectedModule);
        $this->assertEquals($expectedText, $this->_model->getText());
        $this->assertEquals($expectedModule, $this->_model->getModule());
    }

    public function testGetCode()
    {
        $this->assertEquals($this->_expectedModule . '::' . $this->_expectedText, $this->_model->getCode());
        $this->assertEquals($this->_expectedModule . '##' . $this->_expectedText, $this->_model->getCode('##'));
    }
}

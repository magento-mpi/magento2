<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Model_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_TargetRule_Model_Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Enterprise_TargetRule_Model_Rule();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testValidateDataOnEmpty()
    {
        $data = new Varien_Object();
        $this->assertTrue($this->_model->validateData($data), 'True for empty object');
    }

    public function testValidateDataOnValid()
    {
        $data = new Varien_Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => 'Enterprise_TargetRule_Model_Actions_Condition_Combine',
                )
            )
        ));

        $this->assertTrue($this->_model->validateData($data), 'True for right data');
    }

    /**
     * @dataProvider invalidCodesDataProvider
     * @param string $code
     */
    public function testValidateDataOnInvalidCode($code)
    {
        $data = new Varien_Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => 'Enterprise_TargetRule_Model_Actions_Condition_Combine',
                    'attribute' => $code,
                )
            )
        ));
        $this->assertCount(1, $this->_model->validateData($data), 'Error for invalid attribute code');
    }

    /**
     * @return array
     */
    public static function invalidCodesDataProvider()
    {
        return array(
            array(''),
            array('_'),
            array('123'),
            array('!'),
            array(str_repeat('2', 256)),
        );
    }


    /**
     * @expectedException Mage_Core_Exception
     */
    public function testValidateDataOnInvalidType()
    {
        $data = new Varien_Object();
        $data->setRule(array(
                'actions' => array(
                    'test' => array(
                        'type' => 'Enterprise_TargetRule_Invalid',
                    )
                )
            )
        );
        $this->_model->validateData($data);
    }
}

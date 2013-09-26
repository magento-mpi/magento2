<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TargetRule_Model_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TargetRule_Model_Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_TargetRule_Model_Rule');
    }

    public function testValidateDataOnEmpty()
    {
        $data = new Magento_Object();
        $this->assertTrue($this->_model->validateData($data), 'True for empty object');
    }

    public function testValidateDataOnValid()
    {
        $data = new Magento_Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => 'Magento_TargetRule_Model_Actions_Condition_Combine',
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
        $data = new Magento_Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => 'Magento_TargetRule_Model_Actions_Condition_Combine',
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
     * @expectedException Magento_Core_Exception
     */
    public function testValidateDataOnInvalidType()
    {
        $data = new Magento_Object();
        $data->setRule(array(
                'actions' => array(
                    'test' => array(
                        'type' => 'Magento_TargetRule_Invalid',
                    )
                )
            )
        );
        $this->_model->validateData($data);
    }
}

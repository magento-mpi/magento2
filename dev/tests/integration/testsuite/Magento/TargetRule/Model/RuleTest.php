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
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\TargetRule\Model\Rule');
    }

    public function testValidateDataOnEmpty()
    {
        $data = new \Magento\Object();
        $this->assertTrue($this->_model->validateData($data), 'True for empty object');
    }

    public function testValidateDataOnValid()
    {
        $data = new \Magento\Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => '\Magento\TargetRule\Model\Actions\Condition\Combine',
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
        $data = new \Magento\Object();
        $data->setRule(array(
            'actions' => array(
                'test' => array(
                    'type' => '\Magento\TargetRule\Model\Actions\Condition\Combine',
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
     * @expectedException \Magento\Core\Exception
     */
    public function testValidateDataOnInvalidType()
    {
        $data = new \Magento\Object();
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

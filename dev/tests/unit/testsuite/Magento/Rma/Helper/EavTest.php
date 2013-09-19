<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Helper;

class EavTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Rma\Helper\Eav(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false)
        );
    }

    /**
     * @param array $attributeValidateRules
     * @param array $additionalClasses
     * @dataProvider getAdditionalTextElementClassesDataProvider
     */
    public function testGetAdditionalTextElementClasses($validateRules, $additionalClasses)
    {
        $attributeMock = new \Magento\Object(
            array('validate_rules' => $validateRules)
        );
        $this->assertEquals($this->_model->getAdditionalTextElementClasses($attributeMock), $additionalClasses);
    }

    public function getAdditionalTextElementClassesDataProvider()
    {
        return array(
            array(
                array(),
                array()
            ),
            array(
                array('min_text_length' => 10),
                array('validate-length', 'minimum-length-10')
            ),
            array(
                array('max_text_length' => 20),
                array('validate-length', 'maximum-length-20')
            ),
            array(
                array('min_text_length' => 10, 'max_text_length' => 20),
                array('validate-length', 'minimum-length-10', 'maximum-length-20')
            ),
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Config_Structure_Reader();
    }

    public function testConvertCorrectlyConvertsConfigStructureToArray()
    {
        $testDom = dirname(dirname(__DIR__)) . '/_files/system_2.xml';
        $expectedArray = array(
            'config' => array(
                'system' => array(
                    'tabs' => array(
                        array(
                            'id' => 'tab_1',
                            'label' => 'Tab 1 New'
                        )
                    ),
                    'sections' => array(
                        array(
                            'id' => 'section_1',
                            'label' => 'Section 1 New',
                            'groups' => array(

                            )
                        )
                    )
                )
            )
        );
        $this->assertEquals($cachedObject, $this->_model->getConfiguration());
    }
}

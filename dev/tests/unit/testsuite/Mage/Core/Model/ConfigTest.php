<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_model;

    /**
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($data)
    {
        $this->_model = new Mage_Core_Model_Config($data);
        $this->assertInstanceOf('Mage_Core_Model_Config_Options', $this->_model->getOptions());
    }

    public function constructorDataProvider()
    {
        return array(
            array('data' => null),
            array('data' => array()),
            array('data' => new Varien_Simplexml_Element('<body></body>')),
        );
    }

}
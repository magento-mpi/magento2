<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Source_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Source_Design
     */
    protected $_model = null;

    public static function setUpBeforeClass()
    {
        Mage::getConfig()->getOptions()->setDesignDir(__DIR__ . '/_files/design');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Source_Design;
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testGetAllOptionsSorting()
    {
        $fixture = array(
            array(
                'label' => 'Default',
                'value' => array(
                    array(
                        'label' => 'Default (incompatible version)',
                        'value' => 'default/default',
                    ),
                    array(
                        'label' => 'Theme G (incompatible version)',
                        'value' => 'default/g',
                    ),
                ),
            ),
            array(
                'label' => 'Package A',
                'value' => array(
                    array(
                        'label' => 'Theme D (incompatible version)',
                        'value' => 'a/d',
                    ),
                ),
            ),
            array(
                'label' => 'Package B',
                'value' => array(
                    array(
                        'label' => 'Theme E (incompatible version)',
                        'value' => 'b/e',
                    ),
                ),
            ),
        );
        //print_r($this->_model->getAllOptions(false));die;
        $this->assertSame($fixture, $this->_model->getAllOptions(false));
    }

    public function testGetThemeOptionsSorting()
    {
        $fixture = array(
            array(
                'label' => 'Default',
                'value' => array(
                    array(
                        'label' => 'Default (incompatible version)',
                        'value' => 'default/default',
                    ),
                    array(
                        'label' => 'Theme G (incompatible version)',
                        'value' => 'default/g',
                    ),
                ),
            ),
            array(
                'label' => 'Package A',
                'value' => array(
                    array(
                        'label' => 'Theme D (incompatible version)',
                        'value' => 'a/d',
                    ),
                ),
            ),
            array(
                'label' => 'Package B',
                'value' => array(
                    array(
                        'label' => 'Theme E (incompatible version)',
                        'value' => 'b/e',
                    ),
                ),
            ),
        );
        $this->assertSame($fixture, $this->_model->getThemeOptions());
    }

    public function testGetOptions()
    {
        $this->assertSame($this->_model->getAllOptions(false), $this->_model->getOptions());
    }
}

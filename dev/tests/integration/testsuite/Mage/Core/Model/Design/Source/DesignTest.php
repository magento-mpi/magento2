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

/**
 * @group module:Mage_Core
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

    public function testGetAllOptionsSorting()
    {
        $fixture = array(0 => array(
                'label' => 'X / X',
                'value' => array(
                    0 => array(
                        'label' => 'x (incompatible version)',
                        'value' => 'b/e/x',
                    ),
                ),
            ),
            1 => array(
                'label' => 'Y / A',
                'value' => array(
                    0 => array(
                        'label' => 'default (incompatible version)',
                        'value' => 'default/g/default',
                    ),
                ),
            ),
            2 => array(
                'label' => 'Y / Y',
                'value' => array(
                    0 => array(
                        'label' => 'default (incompatible version)',
                        'value' => 'default/default/default',
                    ),
                ),
            ),
            3 => array(
                'label' => 'Z / Z',
                'value' => array(
                    0 => array(
                        'label' => 'y (incompatible version)',
                        'value' => 'a/d/y',
                    ),
                ),
            ),
        );
        $this->assertSame($fixture, $this->_model->getAllOptions(false));
    }

    public function testGetThemeOptionsSorting()
    {
        $fixture = array(
            0 => array(
                'label' => 'X',
                'value' => array(
                    0 => array(
                        'label' => 'X (incompatible version)',
                        'value' => 'b/e',
                    ),
                ),
            ),
            1 => array(
                'label' => 'Y',
                'value' => array(
                    0 => array(
                        'label' => 'A (incompatible version)',
                        'value' => 'default/g',
                    ),
                    1 => array(
                        'label' => 'Y (incompatible version)',
                        'value' => 'default/default',
                    ),
                ),
            ),
            2 => array(
                'label' => 'Z',
                'value' => array(
                    0 => array(
                        'label' => 'Z (incompatible version)',
                        'value' => 'a/d',
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

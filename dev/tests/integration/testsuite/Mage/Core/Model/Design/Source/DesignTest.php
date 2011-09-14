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
        Mage::unregister('_singleton/core/design_package');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Source_Design;
    }

    public function testGetAllOptionsSorting()
    {
        $fixture = array(0 => array(
                'label' => 'a',
                'value' => array(
                    0 => array(
                        'label' => 'd',
                        'value' => 'a/d',
                    ),
                ),
            ),
            1 => array(
                'label' => 'b',
                'value' => array(
                    0 => array(
                        'label' => 'e',
                        'value' => 'b/e',
                    ),
                ),
            ),
            2 => array(
                'label' => 'default',
                'value' => array(
                    0 => array(
                        'label' => 'default',
                        'value' => 'default/default',
                    ),
                    1 => array(
                        'label' => 'g',
                        'value' => 'default/g',
                    ),
                ),
            )
        );
        $this->assertSame($fixture, $this->_model->getAllOptions(false));
    }
}

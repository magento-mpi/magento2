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
 * Layout integration tests
 */
class Mage_Core_Model_LayoutTestBase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => dirname(__FILE__) . '/_files/design',
        ));
        Mage::getDesign()->setDesignTheme('test/default/default');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $dataStructure = Mage::getObjectManager()->create('Magento_Data_Structure');
        $layoutParams = array(
            'structure' => $dataStructure
        );

        $this->_layout = Mage::getModel('Mage_Core_Model_Layout', $layoutParams);
        $this->_layout->getUpdate()->addHandle('layout_test_handle_main');
        $this->_layout->getUpdate()->load('layout_test_handle_extra');
    }

    protected function tearDown()
    {
        $this->_layout = null;
    }
}

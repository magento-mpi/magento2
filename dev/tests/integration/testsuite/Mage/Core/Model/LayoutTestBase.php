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

    protected function setUp()
    {
        /** @var $themeUtility Mage_Core_Utility_Theme */
        $themeUtility = Mage::getModel('Mage_Core_Utility_Theme', array(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design',
            Mage::getDesign()
        ));
        $themeUtility->registerThemes()->setDesignTheme('test/default', 'frontend');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');

        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_layout->getUpdate()->addHandle('layout_test_handle_main');
        $this->_layout->getUpdate()->load('layout_test_handle_extra');
    }

    protected function tearDown()
    {
        $this->_layout = null;
    }
}

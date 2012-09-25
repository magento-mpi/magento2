<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for downloadable tab tests
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_TestCaseAbstract
        extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
     */
    protected $_block;

    /**
     * Store old display_errors ini option value here
     *
     * @var int
     */
    protected $_oldDisplayErrors;

    /**
     * Store old error_reporting ini option value here
     *
     * @var int
     */
    protected $_oldErrorLevel;

    /**
     * Store old isDeveloperMode value here
     *
     * @var boolean
     */
    protected $_oldIsDeveloperMode;

    protected function setUp()
    {
        parent::setUp();

        $this->_oldDisplayErrors  = ini_get('display_errors');
        $this->_oldErrorLevel = error_reporting();
        $this->_oldIsDeveloperMode = Mage::getIsDeveloperMode();
    }

    protected function tearDown()
    {
        ini_set('display_errors', $this->_oldDisplayErrors);
        error_reporting($this->_oldErrorLevel);
        Mage::setIsDeveloperMode($this->_oldIsDeveloperMode);

        unset($this->_block);

        parent::tearDown();
    }

    /**
     * Return block will dependencies
     *
     * @param string $className
     * @return Mage_Backend_Block_Template|null
     */
    protected function _getBlockInstance($className)
    {
        if (Magento_Autoload::getInstance()->classExists($className)) {
            return new $className(
                $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Session', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false),
                $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false)
            );
        }

        return null;
    }
}

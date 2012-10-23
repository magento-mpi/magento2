<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_Block_Customer_Wishlist_ItemsTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Wishlist_Block_Abstract
     *
     */
    protected $_blockInjections = array(
        'Mage_Core_Controller_Request_Http',
        'Mage_Core_Model_Layout',
        'Mage_Core_Model_Event_Manager',
        'Mage_Core_Model_Translate',
        'Mage_Core_Model_Cache',
        'Mage_Core_Model_Design_Package',
        'Mage_Core_Model_Session',
        'Mage_Core_Model_Store_Config',
        'Mage_Core_Controller_Varien_Front',
    );

    public function testGetColumns()
    {
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Mage_Wishlist_Block_Customer_Wishlist_Items', 'test');
        $child = $this->getMock('Mage_Core_Block_Text', array('isEnabled'),
            $this->_prepareConstructorArguments());
        $child->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }

    /**
 * List of block constructor arguments
 *
 * @return array
 */
    protected function _prepareConstructorArguments()
    {
        $arguments = array();
        foreach ($this->_blockInjections as $injectionClass) {
            $arguments[] = Mage::getModel($injectionClass);
        }
        return $arguments;
    }
}

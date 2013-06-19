<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Block_Role_Grid_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Role_Grid_User
     */
    protected $_block;

    public function setUp()
    {
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $layout->createBlock('Mage_User_Block_Role_Grid_User');
    }

    public function testPreparedCollection()
    {
        $this->_block->toHtml();
        $this->assertInstanceOf('Mage_User_Model_Resource_Role_User_Collection', $this->_block->getCollection());
    }
}

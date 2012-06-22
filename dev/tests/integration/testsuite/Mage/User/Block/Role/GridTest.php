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
 * @group module:Mage_User
 */
class Mage_User_Block_Role_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Role_Grid
     */
    protected $_block;

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();
        $this->_block = $layout->createBlock('Mage_User_Block_Role_Grid');
    }

    public function testPreparedCollection()
    {
        $this->_block->toHtml();
        $this->assertInstanceOf('Mage_User_Model_Resource_Role_Collection', $this->_block->getCollection());
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Block_Role_Grid_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Block_Role_Grid_User
     */
    protected $_block;

    protected function setUp()
    {
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $this->_block = $layout->createBlock('Magento_User_Block_Role_Grid_User');
    }

    public function testPreparedCollection()
    {
        $this->_block->toHtml();
        $this->assertInstanceOf('Magento_User_Model_Resource_Role_User_Collection', $this->_block->getCollection());
    }
}

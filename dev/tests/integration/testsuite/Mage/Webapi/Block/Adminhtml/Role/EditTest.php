<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Block_Adminhtml_Role_Edit block
 */
class Mage_Webapi_Block_Adminhtml_Role_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Block_Adminhtml_Role_Edit
     */
    protected $_block = null;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $this->_block = $layout->createBlock('Mage_Webapi_Block_Adminhtml_Role_Edit');
    }

    /**
     * Clear clock
     */
    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * Test setApiRole existing user
     *
     * @dataProvider apiRoleDataProvider
     *
     * @param Varien_Object $apiRole
     * @param string $expectedHeader
     */
    public function testGetHeaderText($apiRole, $expectedHeader)
    {
        $this->_block->setApiRole($apiRole);
        $this->assertEquals($expectedHeader, $this->_block->getHeaderText());
    }

    /**
     * Data provider for testSetApiRole
     *
     * @return array
     */
    public function apiRoleDataProvider()
    {
        return array(
            'new role' => array(
                new Varien_Object(),
                "New API Role"
            ),
            'existing role'  => array(
                new Varien_Object(array('id' => 1, 'role_name' => 'test <b>role</b>')),
                "Edit Role 'test &lt;b&gt;role&lt;/b&gt;'"
            ),
        );
    }
}

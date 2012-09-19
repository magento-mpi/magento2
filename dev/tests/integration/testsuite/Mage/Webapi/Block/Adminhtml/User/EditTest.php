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
 * Test for Mage_Webapi_Block_Adminhtml_User_Edit block
 */
class Mage_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit
     */
    protected $_block = null;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $this->_block = $layout->createBlock('Mage_Webapi_Block_Adminhtml_User_Edit');
    }

    /**
     * Clear clock
     */
    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * Test setApiUser existing user
     *
     * @dataProvider apiUserDataProvider
     *
     * @param Varien_Object $apiUser
     * @param string $expectedHeader
     */
    public function testSetApiUser($apiUser, $expectedHeader)
    {
        $this->_block->setApiUser($apiUser);

        $this->assertEquals($expectedHeader, $this->_block->getHeaderText());
        $this->assertEquals($apiUser, $this->_block->getApiUser());
        $this->_block->toHtml();
        $this->assertEquals($apiUser, $this->_block->getChildBlock('form')->getApiUser());
    }

    /**
     * Data provider for testSetApiUser
     *
     * @return array
     */
    public function apiUserDataProvider()
    {
        return array(
            'new user' => array(
                new Varien_Object(),
                "New User"
            ),
            'existing user'  => array(
                new Varien_Object(array('id' => 1, 'user_name' => 'test <b>user</b>', 'role_id' => 1)),
                "Edit User 'test &lt;b&gt;user&lt;/b&gt;'"
            ),
        );
    }
}

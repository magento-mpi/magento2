<?php
/**
 * Test for Mage_Webapi_Block_Adminhtml_User_Edit block
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit
     */
    protected $_block;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        $this->_layout = Mage::getObjectManager()->get('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Mage_Webapi_Block_Adminhtml_User_Edit');
    }

    /**
     * Clear clock
     */
    protected function tearDown()
    {
        $this->_layout = null;
        $this->_block = null;
    }

    /**
     * Test setApiUser existing user
     *
     * @magentoAppIsolation enabled
     * @dataProvider apiUserDataProvider
     *
     * @param Varien_Object $apiUser
     * @param string $expectedHeader
     */
    public function testSetApiUser($apiUser, $expectedHeader)
    {
        // TODO Move to unit tests after MAGETWO-4015 complete
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
                "New API User"
            ),
            'existing user'  => array(
                new Varien_Object(array('id' => 1, 'api_key' => 'test_key',
                    'contact_email' => 'test@email.com', 'role_id' => 1)),
                "Edit API User 'test_key'"
            ),
        );
    }
}

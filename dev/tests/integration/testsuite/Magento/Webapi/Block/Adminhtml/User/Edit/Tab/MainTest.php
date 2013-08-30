<?php
/**
 * Test for Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Webapi_Block_Adminhtml_User_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager = Mage::getObjectManager();
        $this->_layout = $this->_objectManager->get('Magento_Core_Model_Layout');
        $this->_blockFactory = $this->_objectManager->get('Magento_Core_Model_BlockFactory');
        $this->_block = $this->_blockFactory->createBlock('Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main');
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento_Core_Model_Layout');
        unset($this->_objectManager, $this->_urlBuilder, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method.
     *
     * @dataProvider prepareFormDataProvider
     * @param Magento_Object $apiUser
     * @param array $formElements
     */
    public function testPrepareForm($apiUser, array $formElements)
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        $this->assertEmpty($this->_block->getForm());

        $this->_block->setApiUser($apiUser);
        $this->_block->toHtml();

        $form = $this->_block->getForm();
        $this->assertInstanceOf('Magento_Data_Form', $form);
        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->assertInstanceOf('Magento_Data_Form_Element_Fieldset', $fieldset);
        $elements = $fieldset->getElements();
        foreach ($formElements as $elementId) {
            $element = $elements->searchById($elementId);
            $this->assertNotEmpty($element, "Element '$elementId' is not found in form fieldset");
            $this->assertEquals($apiUser->getData($elementId), $element->getValue());
        }
    }

    /**
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return array(
            'Empty API User' => array(
                new Magento_Object(),
                array(
                    'company_name',
                    'contact_email',
                    'api_key',
                    'secret'
                )
            ),
            'New API User' => array(
                new Magento_Object(array(
                    'company_name' => 'Company',
                    'contact_email' => 'mail@example.com',
                    'api_key' => 'API Key',
                    'secret' => 'API Secret',
                    'role_id' => 1
                )),
                array(
                    'company_name',
                    'contact_email',
                    'api_key',
                    'secret'
                )
            ),
            'Existed API User' => array(
                new Magento_Object(array(
                    'id' => 1,
                    'company_name' => 'Company',
                    'contact_email' => 'mail@example.com',
                    'api_key' => 'API Key',
                    'secret' => 'API Secret',
                    'role_id' => 1
                )),
                array(
                    'user_id',
                    'company_name',
                    'contact_email',
                    'api_key',
                    'secret'
                )
            )
        );
    }
}

<?php
/**
 * Test for Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
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
     * @var Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager = Mage::getObjectManager();
        $this->_layout = $this->_objectManager->get('Magento_Core_Model_Layout');
        $this->_blockFactory = $this->_objectManager->get('Magento_Core_Model_BlockFactory');
        $this->_block = $this->_blockFactory->createBlock('Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main');
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento_Core_Model_Layout');
        $this->_objectManager->removeSharedInstance('Magento_Core_Model_BlockFactory');
        unset($this->_objectManager, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method.
     *
     * @dataProvider prepareFormDataProvider
     * @param Magento_Object $apiRole
     * @param array $formElements
     */
    public function testPrepareForm($apiRole, array $formElements)
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete
        $this->assertEmpty($this->_block->getForm());

        $this->_block->setApiRole($apiRole);
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
            $this->assertEquals($apiRole->getData($elementId), $element->getValue());
        }
    }

    /**
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return array(
            'Empty API Role' => array(
                new Magento_Object(),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'New API Role' => array(
                new Magento_Object(array(
                    'role_name' => 'Role'
                )),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'Existed API Role' => array(
                new Magento_Object(array(
                    'id' => 1,
                    'role_name' => 'Role'
                )),
                array(
                    'role_id',
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            )
        );
    }
}

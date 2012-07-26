<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Urlrewrite_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form
     */
    protected $_form = null;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        parent::setUp();

        Mage::register('current_urlrewrite', new Varien_Object(array('id' => 3)));
        $this->_initForm();
    }

    /**
     * Initialize form
     */
    protected function _initForm()
    {
        $layout = new Mage_Core_Model_Layout();
        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Edit_Form */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Edit_Form', 'block');
        $block->toHtml();
        $this->_form = $block->getForm();
    }

    /**
     * Unset block
     */
    protected function tearDown()
    {
        Mage::unregister('current_urlrewrite');
        unset($this->_form);
        parent::tearDown();
    }

    /**
     * Test that form was prepared correctly
     */
    public function testPrepareForm()
    {
        // Test form was configured correctly
        $this->assertInstanceOf('Varien_Data_Form', $this->_form);
        $this->assertNotEmpty($this->_form->getAction());
        $this->assertEquals('edit_form', $this->_form->getId());
        $this->assertEquals('post', $this->_form->getMethod());
        $this->assertTrue($this->_form->getUseContainer());
        $this->assertContains('/id/3', $this->_form->getAction());

        // Check all expected form elements are present
        $expectedElements = array(
            'is_system',
            'id_path',
            'request_path',
            'target_path',
            'options',
            'description',
            'store_id'
        );
        foreach ($expectedElements as $expectedElement) {
            $this->assertNotNull($this->_form->getElement($expectedElement));
        }
    }

    /**
     * Check session data restoring
     */
    public function testSessionRestore()
    {
        // Set urlrewrite data to session
        $sessionValues = array(
            'store_id'     => 1,
            'id_path'      => 'id_path',
            'request_path' => 'request_path',
            'target_path'  => 'target_path',
            'options'      => 'options',
            'description'  => 'description'
        );
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->setUrlrewriteData($sessionValues);
        // Re-init form to use newly set session data
        $this->_initForm();

        // Check that all fields values are restored from session
        foreach ($sessionValues as $field => $value) {
            $this->assertEquals($value, $this->_form->getElement($field)->getValue());
        }
    }

    /**
     * Test store element is hidden when only one store available
     */
    public function testStoreElementSingleStore()
    {
        /** @var $storeElement Varien_Data_Form_Element_Abstract */
        $storeElement = $this->_form->getElement('store_id');
        $this->assertInstanceOf('Varien_Data_Form_Element_Hidden', $storeElement);

        // Check that store value set correctly
        $defaultStore = Mage::app()->getStore(true)->getId();
        $this->assertEquals($defaultStore, $storeElement->getValue());
    }

    /**
     * Test store selection is available and correctly configured
     *
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testStoreElementMultiStores()
    {
        /** @var $storeElement Varien_Data_Form_Element_Abstract */
        $storeElement = $this->_form->getElement('store_id');

        // Check store selection elements has correct type
        $this->assertInstanceOf('Varien_Data_Form_Element_Select', $storeElement);

        // Check store selection elements has correct renderer
        $this->assertInstanceOf('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element',
            $storeElement->getRenderer());

        // Check store elements has expected values
        $storesList = Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm();
        $this->assertInternalType('array', $storeElement->getValues());
        $this->assertNotEmpty($storeElement->getValues());
        $this->assertEquals($storesList, $storeElement->getValues());
    }
}

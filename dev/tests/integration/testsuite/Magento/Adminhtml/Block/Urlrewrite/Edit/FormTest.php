<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Adminhtml_Block_Urlrewrite_Edit_FormTest
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Urlrewrite_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get form instance
     *
     * @param array $args
     * @return Magento_Data_Form
     */
    protected function _getFormInstance($args = array())
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Urlrewrite_Edit_Form */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Urlrewrite_Edit_Form', 'block', array('data' => $args));
        $block->setTemplate(null);
        $block->toHtml();
        return $block->getForm();
    }

    /**
     * Test that form was prepared correctly
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        // Test form was configured correctly
        $form = $this->_getFormInstance(array('url_rewrite' => new Magento_Object(array('id' => 3))));
        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertNotEmpty($form->getAction());
        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $this->assertTrue($form->getUseContainer());
        $this->assertContains('/id/3', $form->getAction());

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
            $this->assertNotNull($form->getElement($expectedElement));
        }
    }

    /**
     * Check session data restoring
     * @magentoAppIsolation enabled
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
        Mage::getModel('Magento_Adminhtml_Model_Session')->setUrlrewriteData($sessionValues);
        // Re-init form to use newly set session data
        $form = $this->_getFormInstance(array('url_rewrite' => new Magento_Object()));

        // Check that all fields values are restored from session
        foreach ($sessionValues as $field => $value) {
            $this->assertEquals($value, $form->getElement($field)->getValue());
        }
    }

    /**
     * Test store element is hidden when only one store available
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testStoreElementSingleStore()
    {
        $form = $this->_getFormInstance(array('url_rewrite' => new Magento_Object(array('id' => 3))));
        /** @var $storeElement Magento_Data_Form_Element_Abstract */
        $storeElement = $form->getElement('store_id');
        $this->assertInstanceOf('Magento_Data_Form_Element_Hidden', $storeElement);

        // Check that store value set correctly
        $defaultStore = Mage::app()->getStore(true)->getId();
        $this->assertEquals($defaultStore, $storeElement->getValue());
    }

    /**
     * Test store selection is available and correctly configured
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testStoreElementMultiStores()
    {
        $form = $this->_getFormInstance(array('url_rewrite' => new Magento_Object(array('id' => 3))));
        /** @var $storeElement Magento_Data_Form_Element_Abstract */
        $storeElement = $form->getElement('store_id');

        // Check store selection elements has correct type
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $storeElement);

        // Check store selection elements has correct renderer
        $this->assertInstanceOf('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element',
            $storeElement->getRenderer());

        // Check store elements has expected values
        $storesList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_System_Store')
            ->getStoreValuesForForm();
        $this->assertInternalType('array', $storeElement->getValues());
        $this->assertNotEmpty($storeElement->getValues());
        $this->assertEquals($storesList, $storeElement->getValues());
    }

    /**
     * Test fields disabled status
     * @dataProvider fieldsStateDataProvider
     * @magentoAppIsolation enabled
     */
    public function testDisabledFields($urlRewrite, $fields)
    {
        $form = $this->_getFormInstance(array('url_rewrite' => $urlRewrite));
        foreach ($fields as $fieldKey => $expected) {
            $this->assertEquals($expected, $form->getElement($fieldKey)->getDisabled());
        }
    }

    /**
     * Data provider for checking fields state
     */
    public function fieldsStateDataProvider()
    {
        return array(
            array(
                new Magento_Object(),
                array(
                    'is_system'    => true,
                    'id_path'      => false,
                    'request_path' => false,
                    'target_path'  => false,
                    'options'      => false,
                    'description'  => false
                )
            ),
            array(
                new Magento_Object(array('id' => 3)),
                array(
                    'is_system'    => true,
                    'id_path'      => false,
                    'request_path' => false,
                    'target_path'  => false,
                    'options'      => false,
                    'description'  => false
                )
            )
        );
    }
}

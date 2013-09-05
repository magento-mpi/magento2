<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_System_Config_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_formFactory = $this->_objectManager->create('Magento_Data_Form_Factory');
    }

    public function testDependenceHtml()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout', array('area' => 'adminhtml'));
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        /** @var $block Magento_Backend_Block_System_Config_Form */
        $block = $layout->createBlock('Magento_Backend_Block_System_Config_Form', 'block');

        /** @var $childBlock Magento_Core_Block_Text */
        $childBlock = $layout->addBlock('Magento_Core_Block_Text', 'element_dependence', 'block');

        $expectedValue = 'dependence_html_relations';
        $this->assertNotContains($expectedValue, $block->toHtml());

        $childBlock->setText($expectedValue);
        $this->assertContains($expectedValue, $block->toHtml());
    }

    /**
     * @covers Magento_Backend_Block_System_Config_Form::initFields
     * @param $section Magento_Backend_Model_Config_Structure_Element_Section
     * @param $group Magento_Backend_Model_Config_Structure_Element_Group
     * @param $field Magento_Backend_Model_Config_Structure_Element_Field
     * @param array $configData
     * @param bool $expectedUseDefault
     * @dataProvider initFieldsInheritCheckboxDataProvider
     */
    public function testInitFieldsUseDefaultCheckbox($section, $group, $field, array $configData, $expectedUseDefault)
    {
        $this->markTestIncomplete('MAGETWO-9058');
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset($section->getId() . '_' . $group->getId(), array());

        /* @TODO Eliminate stub by proper mock / config fixture usage */
        /** @var $block Magento_Backend_Block_System_Config_FormStub */
        $block = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_System_Config_FormStub');
        $block->setScope(Magento_Backend_Block_System_Config_Form::SCOPE_WEBSITES);
        $block->setStubConfigData($configData);
        $block->initFields($fieldset, $group, $section);

        $fieldsetSel = 'fieldset';
        $valueSel = sprintf('input#%s_%s_%s', $section->getId(), $group->getId(), $field->getId());
        $valueDisabledSel = sprintf('%s[disabled="disabled"]', $valueSel);
        $useDefaultSel = sprintf('input#%s_%s_%s_inherit.checkbox', $section->getId(), $group->getId(),
            $field->getId());
        $useDefaultCheckedSel = sprintf('%s[checked="checked"]', $useDefaultSel);
        $fieldsetHtml = $fieldset->getElementHtml();

        $this->assertSelectCount($fieldsetSel, true, $fieldsetHtml, 'Fieldset HTML is invalid');
        $this->assertSelectCount($valueSel, true, $fieldsetHtml, 'Field input not found in fieldset HTML');
        $this->assertSelectCount($useDefaultSel, true, $fieldsetHtml,
            '"Use Default" checkbox not found in fieldset HTML');

        if ($expectedUseDefault) {
            $this->assertSelectCount($useDefaultCheckedSel, true, $fieldsetHtml,
                '"Use Default" checkbox should be checked');
            $this->assertSelectCount($valueDisabledSel, true, $fieldsetHtml,
                'Field input should be disabled');
        } else {
            $this->assertSelectCount($useDefaultCheckedSel, false, $fieldsetHtml,
                '"Use Default" checkbox should not be checked');
            $this->assertSelectCount($valueDisabledSel, false, $fieldsetHtml,
                'Field input should not be disabled');
        }
    }


    /**
     * @covers Magento_Backend_Block_System_Config_Form::initFields
     * @param $section Magento_Backend_Model_Config_Structure_Element_Section
     * @param $group Magento_Backend_Model_Config_Structure_Element_Group
     * @param $field Magento_Backend_Model_Config_Structure_Element_Field
     * @param array $configData
     * @param bool $expectedUseDefault
     * @dataProvider initFieldsInheritCheckboxDataProvider
     * @magentoConfigFixture default/test_config_section/test_group_config_node/test_field_value config value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testInitFieldsUseConfigPath($section, $group, $field, array $configData, $expectedUseDefault)
    {
        $this->markTestIncomplete('MAGETWO-9058');
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset($section->getId() . '_' . $group->getId(), array());

        /* @TODO Eliminate stub by proper mock / config fixture usage */
        /** @var $block Magento_Backend_Block_System_Config_FormStub */
        $block = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_System_Config_FormStub');
        $block->setScope(Magento_Backend_Block_System_Config_Form::SCOPE_DEFAULT);
        $block->setStubConfigData($configData);
        $block->initFields($fieldset, $group, $section);

        $fieldsetSel = 'fieldset';
        $valueSel = sprintf('input#%s_%s_%s', $section->getId(), $group->getId(), $field->getId());
        $fieldsetHtml = $fieldset->getElementHtml();

        $this->assertSelectCount($fieldsetSel, true, $fieldsetHtml, 'Fieldset HTML is invalid');
        $this->assertSelectCount($valueSel, true, $fieldsetHtml, 'Field input not found in fieldset HTML');
    }

    /**
     * @TODO data provider should be static
     * @return array
     */
    public function initFieldsInheritCheckboxDataProvider()
    {
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_BAN_CACHE => true,
        ));
        Magento_Test_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        Mage::app()
            ->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);

        $configMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false, false);
        $configMock->expects($this->any())->method('getConfigurationFiles')
            ->will($this->returnValue(array(__DIR__ . '/_files/test_section_config.xml')));
        $configMock->expects($this->any())->method('getModuleDir')
            ->will($this->returnValue(BP . '/app/code/Magento/Backend/etc'));

        Magento_Test_Helper_Bootstrap::getObjectManager()->configure(array(
            'Magento_Backend_Model_Config_Structure_Reader' => array(
                'parameters' => array('moduleReader' => $configMock)
            )
        ));
        /** @var Magento_Backend_Model_Config_Structure $structure  */
        $structure = Mage::getSingleton('Magento_Backend_Model_Config_Structure');

        /** @var Magento_Backend_Model_Config_Structure_Element_Section $section  */
        $section = $structure->getElement('test_section');

        /** @var Magento_Backend_Model_Config_Structure_Element_Group $group  */
        $group = $structure->getElement('test_section/test_group');

        /** @var Magento_Backend_Model_Config_Structure_Element_Field $field  */
        $field = $structure->getElement('test_section/test_group/test_field');

        $fieldPath = $field->getConfigPath();

        /** @var Magento_Backend_Model_Config_Structure_Element_Field $field  */
        $field2 = $structure->getElement('test_section/test_group/test_field_use_config');

        $fieldPath2 = $field2->getConfigPath();
        return array(
            array($section, $group, $field, array(), true),
            array($section, $group, $field, array($fieldPath => null), false),
            array($section, $group, $field, array($fieldPath => ''), false),
            array($section, $group, $field, array($fieldPath => 'value'), false),
            array($section, $group, $field2, array($fieldPath2 => 'config value'), true),
        );
    }

    public function testInitFormAddsFieldsets()
    {
        Mage::getModel(
            'Magento_Core_Controller_Front_Action',
            array('request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse())
        );
        Mage::app()->getRequest()->setParam('section', 'general');
        /** @var $block Magento_Backend_Block_System_Config_Form */
        $block = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_System_Config_Form');
        $block->initForm();
        $expectedIds = array(
            'general_country' => array(
                'general_country_default' => 'select',
                'general_country_allow' => 'select',
                'general_country_optional_zip_countries' => 'select',
                'general_country_eu_countries' => 'select'
            ),
            'general_region' => array(
                'general_region_state_required' => 'select',
                'general_region_display_all' => 'select'
            ),
            'general_locale' => array(
                'general_locale_timezone' => 'select',
                'general_locale_code' => 'select',
                'general_locale_firstday' => 'select',
                'general_locale_weekend' => 'select'
            ),
            'general_restriction' => array(
                'general_restriction_is_active' => 'select',
                'general_restriction_mode' => 'select',
                'general_restriction_http_redirect' => 'select',
                'general_restriction_cms_page' => 'select',
                'general_restriction_http_status' => 'select'
            ),
            'general_store_information' => array(
                'general_store_information_name' => 'text',
                'general_store_information_phone' => 'text',
                'general_store_information_merchant_country' => 'select',
                'general_store_information_merchant_vat_number' => 'text',
                'general_store_information_validate_vat_number' => 'text',
                'general_store_information_address' => 'textarea',
            ),
            'general_single_store_mode' => array(
                'general_single_store_mode_enabled' => 'select',
            )
        );
        $elements = $block->getForm()->getElements();
        foreach ($elements as $element) {
            /** @var $element Magento_Data_Form_Element_Fieldset */
            $this->assertInstanceOf('Magento_Data_Form_Element_Fieldset', $element);
            $this->assertArrayHasKey($element->getId(), $expectedIds);
            $fields = $element->getElements();
            $this->assertEquals(count($expectedIds[$element->getId()]), count($fields));
            foreach ($element->getElements() as $field) {
                $this->assertArrayHasKey($field->getId(), $expectedIds[$element->getId()]);
                $this->assertEquals($expectedIds[$element->getId()][$field->getId()], $field->getType());
            }
        };
    }
}

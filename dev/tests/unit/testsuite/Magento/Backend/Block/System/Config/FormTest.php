<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_System_Config_Form
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_systemConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldsetFactoryMock;

    protected function setUp()
    {
        $this->_systemConfigMock = $this->getMock('Magento_Backend_Model_Config_Structure',
            array(), array(), '', false, false
        );

        $requestMock = $this->getMock('Magento_Core_Controller_Request_Http',
            array(), array(), '', false, false
        );
        $requestParams = array(
            array('website', '', 'website_code'),
            array('section', '', 'section_code'),
            array('store', '', 'store_code'),
        );
        $requestMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($requestParams));

        $layoutMock = $this->getMock('Magento_Core_Model_Layout',
            array(), array(), '', false, false
        );

        $this->_urlModelMock = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false, false);
        $configFactoryMock = $this->getMock('Magento_Backend_Model_Config_Factory', array(), array(), '', false, false);
        $this->_formFactoryMock = $this->getMock('Magento\Data\Form\Factory', array(), array(), '', false, false);
        $cloneFactoryMock = $this->getMock('Magento_Backend_Model_Config_Clone_Factory',
            array(), array(), '', false, false
        );
        $this->_fieldsetFactoryMock = $this->getMock('Magento_Backend_Block_System_Config_Form_Fieldset_Factory',
            array(), array(), '', false, false
        );
        $this->_fieldFactoryMock = $this->getMock('Magento_Backend_Block_System_Config_Form_Field_Factory',
            array(), array(), '', false, false
        );
        $this->_coreConfigMock = $this->getMock('Magento_Core_Model_Config',
            array(), array(), '', false, false
        );

        $this->_backendConfigMock = $this->getMock('Magento_Backend_Model_Config',
            array(), array(), '', false, false
        );

        $configFactoryMock->expects($this->once())->method('create')
            ->with(array(
                'data' => array('section' => 'section_code', 'website' => 'website_code', 'store' => 'store_code')
            ))
            ->will($this->returnValue($this->_backendConfigMock));

        $this->_backendConfigMock->expects($this->once())->method('load')
            ->will($this->returnValue(array('section1/group1/field1' => 'some_value')));

        $this->_formMock = $this->getMock('Magento\Data\Form',
            array('setParent', 'setBaseUrl', 'addFieldset'), array(), '', false, false
        );
        $data = array(
            'request' => $requestMock,
            'layout' => $layoutMock,
            'urlBuilder' => $this->_urlModelMock,
            'configStructure' => $this->_systemConfigMock,
            'configFactory' => $configFactoryMock,
            'formFactory' => $this->_formFactoryMock,
            'cloneModelFactory' => $cloneFactoryMock,
            'fieldsetFactory' => $this->_fieldsetFactoryMock,
            'fieldFactory' => $this->_fieldFactoryMock,
            'coreConfig' => $this->_coreConfigMock,
        );

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_object = $helper->getObject('Magento_Backend_Block_System_Config_Form', $data);
        $this->_object->setData('scope_id', 1);
    }

    public function testInitFormWithoutSection()
    {
        $this->_formFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_formMock));
        $this->_formMock->expects($this->once())->method('setParent')->with($this->_object);
        $this->_formMock->expects($this->once())->method('setBaseUrl')->with('base_url');
        $this->_urlModelMock->expects($this->any())->method('getBaseUrl')->will($this->returnValue('base_url'));

        $this->_systemConfigMock->expects($this->once())->method('getElement')
            ->with('section_code')->will($this->returnValue(null));

        $this->_object->initForm();
        $this->assertEquals($this->_formMock, $this->_object->getForm());
    }

    public function testInitGroup()
    {
        $this->_formFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_formMock));
        $this->_formMock->expects($this->once())->method('setParent')->with($this->_object);
        $this->_formMock->expects($this->once())->method('setBaseUrl')->with('base_url');
        $this->_urlModelMock->expects($this->any())->method('getBaseUrl')->will($this->returnValue('base_url'));

        $fieldsetRendererMock = $this->getMock('Magento_Backend_Block_System_Config_Form_Fieldset',
            array(), array(), '', false, false
        );
        $this->_fieldsetFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($fieldsetRendererMock));

        $cloneModelMock = $this->getMock('Magento_Backend_Model_Config',
            array('getPrefixes'), array(), '', false, false
        );

        $cloneModelMock->expects($this->once())->method('getPrefixes')->will($this->returnValue(array()));

        $groupMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group',
            array(), array(), '', false, false
        );
        $groupMock->expects($this->once())->method('getFrontendModel')->will($this->returnValue(false));
        $groupMock->expects($this->any())->method('getPath')->will($this->returnValue('section_id_group_id'));
        $groupMock->expects($this->once())->method('getLabel')->will($this->returnValue('label'));
        $groupMock->expects($this->once())->method('getComment')->will($this->returnValue('comment'));
        $groupMock->expects($this->once())->method('isExpanded')->will($this->returnValue(false));
        $groupMock->expects($this->once())->method('populateFieldset');
        $groupMock->expects($this->once())->method('shouldCloneFields')->will($this->returnValue(true));
        $groupMock->expects($this->once())->method('getCloneModel')->will($this->returnValue($cloneModelMock));
        $groupMock->expects($this->once())->method('getData')->will($this->returnValue('some group data'));
        $groupMock->expects($this->once())
            ->method('getDependencies')->with('store_code')->will($this->returnValue(array()));

        $sectionMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Section',
            array(), array(), '', false, false
        );

        $sectionMock->expects($this->once())->method('isVisible')->will($this->returnValue(true));
        $sectionMock->expects($this->once())->method('getChildren')->will($this->returnValue(array($groupMock)));

        $this->_systemConfigMock->expects($this->once())->method('getElement')
            ->with('section_code')->will($this->returnValue($sectionMock));

        $formFieldsetMock = $this->getMock('Magento\Data\Form\Element\Fieldset',
            array(), array(), '', false, false
        );

        $params = array(
            'legend' => 'label',
            'comment' => 'comment',
            'expanded' => false,
            'group' => 'some group data'
        );
        $this->_formMock->expects($this->once())->method('addFieldset')->with('section_id_group_id', $params)
            ->will($this->returnValue($formFieldsetMock));
        $this->_object->initForm();
    }

    /**
     * @dataProvider initFieldsDataProvider
     */
    public function testInitFields($backendConfigValue, $xmlConfig, $configPath, $inherit, $expectedValue)
    {
        // Parameters initialization
        $fieldsetMock = $this->getMock('Magento\Data\Form\Element\Fieldset', array(), array(), '', false, false);
        $groupMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group',
            array(), array(), '', false, false
        );
        $sectionMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Section',
            array(), array(), '', false, false
        );
        $fieldPrefix = 'fieldPrefix';
        $labelPrefix = 'labelPrefix';

        // Field Renderer Mock configuration
        $fieldRendererMock = $this->getMock('Magento_Backend_Block_System_Config_Form_Field',
            array(), array(), '', false, false
        );
        $this->_fieldFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($fieldRendererMock));

        $this->_backendConfigMock->expects($this->once())->method('extendConfig')
            ->with('some/config/path', false, array('section1/group1/field1' => 'some_value'))
            ->will($this->returnValue($backendConfigValue));

        $this->_coreConfigMock->expects($this->any())->method('getNode')->will($this->returnValue($xmlConfig));

        // Field mock configuration
        $fieldMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Field',
            array(), array(), '', false, false
        );
        $fieldMock->expects($this->any())->method('getPath')
            ->will($this->returnValue('section1/group1/field1'));
        $fieldMock->expects($this->any())->method('getConfigPath')
            ->will($this->returnValue($configPath));
        $fieldMock->expects($this->any())->method('getGroupPath')->will($this->returnValue('some/config/path'));
        $fieldMock->expects($this->once())->method('getSectionId')->will($this->returnValue('some_section'));

        $fieldMock->expects($this->once())->method('hasBackendModel')->will($this->returnValue(false));
        $fieldMock->expects($this->once())->method('getDependencies')
            ->with($fieldPrefix)->will($this->returnValue(array()));
        $fieldMock->expects($this->any())->method('getType')->will($this->returnValue('field'));
        $fieldMock->expects($this->once())->method('getLabel')->will($this->returnValue('label'));
        $fieldMock->expects($this->once())->method('getComment')->will($this->returnValue('comment'));
        $fieldMock->expects($this->once())->method('getTooltip')->will($this->returnValue('tooltip'));
        $fieldMock->expects($this->once())->method('getHint')->will($this->returnValue('hint'));
        $fieldMock->expects($this->once())->method('getFrontendClass')->will($this->returnValue('frontClass'));
        $fieldMock->expects($this->once())->method('showInDefault')->will($this->returnValue(false));
        $fieldMock->expects($this->any())->method('showInWebsite')->will($this->returnValue(false));
        $fieldMock->expects($this->once())->method('getData')->will($this->returnValue('fieldData'));
        $fieldMock->expects($this->any())->method('getRequiredFields')->will($this->returnValue(array()));
        $fieldMock->expects($this->any())->method('getRequiredGroups')->will($this->returnValue(array()));


        $fields = array($fieldMock);
        $groupMock->expects($this->once())->method('getChildren')->will($this->returnValue($fields));

        $sectionMock->expects($this->once())->method('getId')->will($this->returnValue('section1'));

        $formFieldMock = $this->getMockForAbstractClass('\Magento\Data\Form\Element\AbstractElement',
            array(), '', false, false, true, array('setRenderer')
        );

        $params = array(
            'name' => 'groups[group1][fields][fieldPrefixfield1][value]',
            'label' => 'label',
            'comment' => 'comment',
            'tooltip' => 'tooltip',
            'hint' => 'hint',
            'value' => $expectedValue,
            'inherit' => $inherit,
            'class' => 'frontClass',
            'field_config' => 'fieldData',
            'scope' => 'stores',
            'scope_id' => 1,
            'scope_label' => '[GLOBAL]',
            'can_use_default_value' => false,
            'can_use_website_value' => false,
        );

        $formFieldMock->expects($this->once())->method('setRenderer')->with($fieldRendererMock);

        $fieldsetMock->expects($this->once())->method('addField')
            ->with('section1_group1_field1', 'field', $params)
            ->will($this->returnValue($formFieldMock));

        $fieldMock->expects($this->once())->method('populateInput');

        $this->_object->initFields($fieldsetMock, $groupMock, $sectionMock, $fieldPrefix, $labelPrefix);
    }

    /**
     * @return array
     */
    public function initFieldsDataProvider()
    {
        $xmlConfig = new Magento_Core_Model_Config_Element('
            <default>
                <some>
                    <config>
                        <path>Config Value</path>
                    </config>
                </some>
            </default>
        ');

        return array(
            array(array('section1/group1/field1' => 'some_value'), false, null, false, 'some_value'),
            array(array(), $xmlConfig, 'some/config/path', true, $xmlConfig->descend('some/config/path')),
        );
    }
}

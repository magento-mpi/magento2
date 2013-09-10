<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_System_Email_Template_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_System_Email_Template_Edit
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configStructureMock;

    protected function setUp()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $registryMock = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false, false);
        $layoutMock = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false, false);
        $helperMock = $this->getMock('Magento_Adminhtml_Helper_Data', array(), array(), '', false, false);
        $menuConfigMock = $this->getMock('Magento_Backend_Model_Menu_Config', array(), array(), '', false, false);
        $menuMock = $this->getMock('Magento_Backend_Model_Menu', array(), array(), '', false, false);
        $menuItemMock = $this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false, false);
        $urlBuilder = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false, false);
        $this->_configStructureMock = $this->getMock('Magento_Backend_Model_Config_Structure',
            array(), array(), '', false, false
        );

        $params = array(
            'urlBuilder' => $urlBuilder,
            'registry' => $registryMock,
            'layout' => $layoutMock,
            'menuConfig' => $menuConfigMock,
            'configStructure' => $this->_configStructureMock,
        );
        $arguments = $objectManager->getConstructArguments('Magento_Adminhtml_Block_System_Email_Template_Edit',
            $params);

        $urlBuilder->expects($this->any())->method('getUrl')->will($this->returnArgument(0));
        $menuConfigMock->expects($this->any())->method('getMenu')->will($this->returnValue($menuMock));
        $menuMock->expects($this->any())->method('get')->will($this->returnValue($menuItemMock));
        $menuItemMock->expects($this->any())->method('getTitle')->will($this->returnValue('Title'));

        $paths = array(
            array(
                'scope' => 'scope_11',
                'scope_id' => 'scope_id_1',
                'path' => 'section1/group1/field1',
            ),
            array(
                'scope' => 'scope_11',
                'scope_id' => 'scope_id_1',
                'path' => 'section1/group1/group2/field1',
            ),
            array(
                'scope' => 'scope_11',
                'scope_id' => 'scope_id_1',
                'path' => 'section1/group1/group2/group3/field1',
            ),
        );
        $templateMock = $this->getMock('Magento_Adminhtml_Model_Email_Template', array(), array(), '', false, false);
        $templateMock->expects($this->once())
            ->method('getSystemConfigPathsWhereUsedCurrently')
            ->will($this->returnValue($paths));

        $registryMock->expects($this->once())->method('registry')
            ->with('current_email_template')->will($this->returnValue($templateMock));

        $layoutMock->expects($this->any())->method('helper')->will($this->returnValue($helperMock));

        $this->_block = $objectManager->getObject('Magento_Adminhtml_Block_System_Email_Template_Edit', $arguments);
    }

    public function testGetUsedCurrentlyForPaths()
    {
        $sectionMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Section',
            array(), array(), '', false, false
        );
        $groupMock1 = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group',
            array(), array(), '', false, false
        );
        $groupMock2 = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group',
            array(), array(), '', false, false
        );
        $groupMock3 = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group',
            array(), array(), '', false, false
        );
        $filedMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Field',
            array(), array(), '', false, false
        );
        $map = array(
            array(array('section1', 'group1'), $groupMock1),
            array(array('section1', 'group1', 'group2'), $groupMock2),
            array(array('section1', 'group1', 'group2', 'group3'), $groupMock3),
            array(array('section1', 'group1', 'field1'), $filedMock),
            array(array('section1', 'group1', 'group2', 'field1'), $filedMock),
            array(array('section1', 'group1', 'group2', 'group3', 'field1'), $filedMock),
        );
        $sectionMock->expects($this->any())->method('getLabel')->will($this->returnValue('Section_1_Label'));
        $groupMock1->expects($this->any())->method('getLabel')->will($this->returnValue('Group_1_Label'));
        $groupMock2->expects($this->any())->method('getLabel')->will($this->returnValue('Group_2_Label'));
        $groupMock3->expects($this->any())->method('getLabel')->will($this->returnValue('Group_3_Label'));
        $filedMock->expects($this->any())->method('getLabel')->will($this->returnValue('Field_1_Label'));

        $this->_configStructureMock->expects($this->any())
            ->method('getElement')->with('section1')->will($this->returnValue($sectionMock));

        $this->_configStructureMock->expects($this->any())
            ->method('getElementByPathParts')->will($this->returnValueMap($map));

        $actual = $this->_block->getUsedCurrentlyForPaths(false);
        $expected = array(
            array(
                array('title' => __('Title'),),
                array('title' => __('Title'), 'url' => 'adminhtml/system_config/',),
                array('title' => 'Section_1_Label', 'url' => 'adminhtml/system_config/edit',),
                array('title' => 'Group_1_Label',),
                array('title' => 'Field_1_Label', 'scope' => __('GLOBAL'),),
            ),
            array(
                array('title' => __('Title'),),
                array('title' => __('Title'), 'url' => 'adminhtml/system_config/',),
                array('title' => 'Section_1_Label', 'url'   => 'adminhtml/system_config/edit',),
                array('title' => 'Group_1_Label',),
                array('title' => 'Group_2_Label',),
                array('title' => 'Field_1_Label', 'scope' => __('GLOBAL'),),
            ),
            array(
                array('title' => __('Title'),),
                array('title' => __('Title'), 'url' => 'adminhtml/system_config/',),
                array('title' => 'Section_1_Label', 'url' => 'adminhtml/system_config/edit',),
                array('title' => 'Group_1_Label',),
                array('title' => 'Group_2_Label',),
                array('title' => 'Group_3_Label',),
                array('title' => 'Field_1_Label', 'scope' => __('GLOBAL'),),
            )
        );
        $this->assertEquals($expected, $actual);
    }
}

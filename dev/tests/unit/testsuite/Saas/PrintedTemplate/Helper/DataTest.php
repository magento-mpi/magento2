<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Saas_PrintedTemplate_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetPrintButtonOnclick()
    {
        // preapre
        $type = uniqid();
        $model = new Magento_Object(array('id' => uniqid()));
        $helper = $this->getMock('Saas_PrintedTemplate_Helper_Data',
            array('_registry', '_getBackendHelper'), array(), '', false
        );
        $helper->expects($this->once())
            ->method('_registry')
            ->with("current_$type")
            ->will($this->returnValue($model));
        $helper->expects($this->once())
            ->method('_getBackendHelper')
            ->will($this->returnValue($this->helper(array(array(
                'adminhtml/print/entity/', array('type' => $type, 'id' => $model->getId()), $result = uniqid()
            )))));

        // act
        $actualResult = $helper->getPrintButtonOnclick($type);

        // assert
        $this->assertEquals("setLocation('$result')", $actualResult);
    }

    /**
     * @dataProvider providerPathsParts
     */
    public function testGetSystemConfigPathsParts($paths, $expectedResult)
    {
        // preapre
        $menuItems = array(
            'Magento_Adminhtml::system'        => $this->itemMock('System'),
            'Magento_Adminhtml::system_config' => $this->itemMock('Configuration'),
        );
        $configElements = array(
            array('sales_pdf', $this->elementMock('PDF Print-outs')),
            array(array('sales_pdf', 'invoice'), $this->elementMock('Invoice')),
            array(array('sales_pdf', 'invoice', 'printed_template'), $this->elementMock('Printed Template')),
        );
        $urls = array(
            array('adminhtml/system_config/', 'http://url/system_config/'),
            array('adminhtml/system_config/edit', array('section' => 'sales_pdf'), 'http://url/sales_pdf/'),
        );

        $helper = $this->getMock(
            'Saas_PrintedTemplate_Helper_Data',
            array('_getBackendHelper', '_getMenuConfig', '_getConfigStructure'),
            array(),
            '',
            false
        );
        $helper->expects($this->any())
            ->method('_getBackendHelper')
            ->will($this->returnValue($this->helper($urls)));
        $helper->expects($this->any())
            ->method('_getMenuConfig')
            ->will($this->returnValue($this->menuConfigMock($this->menuMock($menuItems))));
        $helper->expects($this->any())
            ->method('_getConfigStructure')
            ->will($this->returnValue($this->configStructureMock($configElements)));

        // act
        $result = $helper->getSystemConfigPathsParts($paths);

        // assert
        $this->assertEquals($expectedResult, $result);
    }

    public function providerPathsParts()
    {
        return array(
            array(
                array(
                    array(
                        'config_id' => '28',
                        'scope' => 'default',
                        'scope_id' => '0',
                        'path' => 'sales_pdf/invoice/printed_template',
                        'value' => '1',
                    ),
                ),
                array(
                    array(
                        array(
                            'title' => 'System-tr'
                        ),
                        array(
                            'title' => 'Configuration-tr',
                            'url' => 'http://url/system_config/',
                        ),
                        array (
                            'title' => 'PDF Print-outs',
                            'url' => 'http://url/sales_pdf/',
                        ),
                        array (
                            'title' => 'Invoice'
                        ),
                        array (
                            'title' => 'Printed Template',
                            'scope' => 'GLOBAL-tr',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns helper mock
     *
     * @return Magento_Core_Helper_Abstract
     */
    protected function helper(array $urls = array())
    {
        $helper = $this->getMock('Magento_Core_Helper_Abstract', array('__', 'getUrl'), array(), '', false);
        $helper->expects($this->any())
            ->method('__')
            ->will(
                $this->returnCallback(
                    function ($msg)
                    {
                        return "$msg-tr";
                    }
                )
            );
        $helper->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap($urls));


        return $helper;
    }

    /**
     * Returns menu config mock
     *
     * @param Magento_Backend_Model_Menu $menu
     * @return Magento_Object
     */
    protected function menuConfigMock(Magento_Backend_Model_Menu $menu)
    {
        return new Magento_Object(array('menu' => $menu));
    }

    /**
     * Returns menu mock
     *
     * @param array $items
     * @return Magento_Backend_Model_Menu
     */
    protected function menuMock(array $items)
    {
        $itemsMap = array();
        foreach ($items as $key => $item) {
            $itemsMap[] = array($key, $item);
        }

        $menu = $this->getMockBuilder('Magento_Backend_Model_Menu')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $menu->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($itemsMap));

        return $menu;
    }

    /**
     * Returns item mock
     *
     * @param string $title
     * @return Magento_Backend_Model_Menu_Item
     */
    protected function itemMock($title)
    {
        $item = $this->getMockBuilder('Magento_Backend_Model_Menu_Item')
            ->setMethods(array('getModuleHelper', 'getTitle'))
            ->disableOriginalConstructor()
            ->getMock();
        $item->expects($this->any())
            ->method('getModuleHelper')
            ->will($this->returnValue($this->helper()));
        $item->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($title));

        return $item;
    }

    protected function configStructureMock(array $elements)
    {
        $structure = $this->getMockBuilder('Magento_Backend_Model_Config_Structure')
            ->setMethods(array('getElement', 'getElementByPathParts'))
            ->disableOriginalConstructor()
            ->getMock();
        $structure->expects($this->any())
            ->method('getElement')
            ->will($this->returnValueMap($elements));
        $structure->expects($this->any())
            ->method('getElementByPathParts')
            ->will($this->returnValueMap($elements));

        return $structure;
    }

    protected function elementMock($label)
    {
        return new Magento_Object(array('label' => $label));
    }
}

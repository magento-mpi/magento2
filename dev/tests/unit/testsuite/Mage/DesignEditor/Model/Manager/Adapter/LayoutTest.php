<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Manager_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @cove Mage_DesignEditor_Model_History_Manager_Adapter_Layout::addAction
     * @dataProvider moveChangeData
     */
    public function testAddAction($name, $handle, $type, $action, $data)
    {
        /** @var $layoutMock Mage_DesignEditor_Model_History_Manager_Adapter_Layout */
        $layoutMock = $this->getMock(
            'Mage_DesignEditor_Model_History_Manager_Adapter_Layout', null, array(), '', false
        );
        $layoutMock->setHandle($handle)->setType($type)->setName($name)->addAction($action, $data);

        $this->assertEquals($this->expectedMoveActionData(), $layoutMock->getData());
    }

    /**
     * @cove Mage_DesignEditor_Model_History_Manager_Adapter_Layout::render
     * @dataProvider changeData
     */
    public function testRenderRemove($expectedXml, $name, $handle, $type, $action, $data)
    {
        /** @var $layoutMock Mage_DesignEditor_Model_History_Manager_Adapter_Layout */
        $layoutMock = $this->getMock(
            'Mage_DesignEditor_Model_History_Manager_Adapter_Layout', null, array(), '', false
        );
        $xmlObject = new Varien_Simplexml_Element('<layout></layout>');
        $layoutMock->setHandle($handle)->setHandleObject($xmlObject)->setType($type)->setName($name)
            ->addAction($action, $data)->render();

        $this->assertXmlStringEqualsXmlFile(
            realpath(__DIR__) . '/../../_files/history/layout/' . $expectedXml, $xmlObject->asNiceXml()
        );
    }

    public function changeData()
    {
        return array(
            array(
                'move.xml', 'customer_account_navigation', 'customer_account', 'layout', 'move', array(
                    'destination_container' => 'top.menu',
                    'after'                 => '-',
                    'as'                    => 'customer_account_navigation_alias',
                )
            ),
            array(
                'remove.xml', 'customer_account_navigation', 'customer_account', 'layout', 'remove', array()
            ),
        );
    }

    public function moveChangeData()
    {
        return array(
            array('customer_account_navigation', 'customer_account', 'layout', 'move', array(
                'destination_container' => 'top.menu',
                'after'                 => '-',
                'as'                    => 'customer_account_navigation_alias',
            )));
    }

    public function expectedMoveActionData()
    {
        return array(
            'actions' => array(
                'move' => array(
                    'destination_container' => 'top.menu',
                    'after'                 => '-',
                    'as'                    => 'customer_account_navigation_alias',
                )
            ),
            'handle' => 'customer_account',
            'name'   => 'customer_account_navigation',
            'type'   => 'layout'
        );
    }
}

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

class Mage_DesignEditor_Model_History_Compact_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * Layout object
     *
     * @var Mage_DesignEditor_Model_History_Compact_Layout
     */
    protected $_layout;

    /**
     * Init test environment
     */
    protected function setUp()
    {
        $this->_layout = new Mage_DesignEditor_Model_History_Compact_Layout;
    }

    /**
     * Get mocked object of collection
     *
     * @param array $data
     * @return Mage_DesignEditor_Model_Change_Collection|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _mockCollection(array $data)
    {
        /** @var $collectionMock Mage_DesignEditor_Model_Change_Collection */
        $collectionMock = $this->getMock(
            'Mage_DesignEditor_Model_Change_Collection', array('_init'), array(), '', true
        );
        foreach ($data as $item) {
            $changeClassName = Mage_DesignEditor_Model_Change_Factory::getClass($item);
            /** @var $itemMock Mage_DesignEditor_Model_Change_LayoutAbstract */
            $itemMock = $this->getMock(
                $changeClassName, array('getLayoutUpdateData', 'getLayoutDirective'), array(), '', false
            );
            $itemMock->setData($item);
            $collectionMock->addItem($itemMock);
        }
        return $collectionMock;
    }

    /**
     * Test compact logic with wrong collection
     *
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Compact collection is missed
     */
    public function testBrokenCompactCollection()
    {
        $this->_layout->compact();
    }

    /**
     * Test compact logic
     *
     * @param array $data
     * @param array $expectedData
     * @dataProvider removeDataSamples
     */
    public function testCompact($data, $expectedData)
    {
        $collection = $this->_mockCollection($data);
        $this->_layout->compact($collection);

        $compactedData = array();
        /** @var $change Mage_DesignEditor_Model_Change_LayoutAbstract */
        foreach ($collection as $change) {
            $compactedData[] = $change->getData();
        }
        $this->assertEquals($expectedData, $compactedData);
    }

    /**
     * DataProvider with remove directives
     *
     * @return array
     */
    public function removeDataSamples()
    {
        return array(
            array(array(
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'head'),
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head',
                    'origin_container' => 'root', 'destination_container' => 'footer'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'page.pools'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'head'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'page.pools'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'page.pools'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'head')
            ), array(
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'page.pools'),
                array('type' => 'layout', 'action_name' => 'remove', 'element_name' => 'head'),
            )),
            array(array(
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 0,
                    'origin_container' => 'root', 'destination_container' => 'footer', 'destination_order' => 1),
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 1,
                    'origin_container' => 'footer', 'destination_container' => 'page.pools', 'destination_order' => 2),
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 2,
                    'origin_container' => 'page.pools', 'destination_container' => 'footer', 'destination_order' => 3),
            ), array(
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 2,
                    'origin_container' => 'page.pools', 'destination_container' => 'footer', 'destination_order' => 3)
            )),
            array(array(
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 0,
                    'origin_container' => 'root', 'destination_container' => 'footer', 'destination_order' => 1),
                array('type' => 'layout', 'action_name' => 'move', 'element_name' => 'head', 'origin_order' => 1,
                    'origin_container' => 'footer', 'destination_container' => 'root', 'destination_order' => 0),
            ), array(
            )),
        );
    }
}

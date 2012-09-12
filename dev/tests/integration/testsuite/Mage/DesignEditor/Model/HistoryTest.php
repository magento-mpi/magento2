<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_HistoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_History
     */
    protected $_historyObject;

    /**
     * Get clear history model
     *
     * @return Mage_DesignEditor_Model_History
     */
    protected function getClearHistoryModel()
    {
        return $this->_historyObject = Mage::getModel('Mage_DesignEditor_Model_History');
    }

    /**
     * Add change test
     *
     * @dataProvider getChange
     */
    public function testAddChange($change)
    {
        $historyModel = $this->getClearHistoryModel();
        $collection = $historyModel->addChange($change)->getChanges();

        $this->assertEquals(array($change), $collection->toArray());
    }

    /**
     * Add change with invalid data test
     *
     * @dataProvider getInvalidChange
     * @expectedException Magento_Exception
     */
    public function testAddChangeWithInvalidData($change)
    {
        $historyModel = $this->getClearHistoryModel();
        $collection = $historyModel->addChange($change)->getChanges();

        $this->assertEquals(array($change), $collection->toArray());
    }

    /**
     * Set changes test
     *
     * @dataProvider getChanges
     */
    public function testSetChanges($changes)
    {
        $historyModel = $this->getClearHistoryModel();
        $collection = $historyModel->setChanges($changes)->getChanges();

        $this->assertEquals($changes, $collection->toArray());
    }

    /**
     * Test output(renderer)
     *
     * @dataProvider getChanges
     */
    public function testOutput($changes)
    {
        $historyModel = $this->getClearHistoryModel();
        /** @var $layoutRenderer Mage_DesignEditor_Model_History_Renderer_LayoutUpdate */
        $layoutRenderer = Mage::getModel('Mage_DesignEditor_Model_History_Renderer_LayoutUpdate');

        /** @var $collection Mage_DesignEditor_Model_Change_Collection */
        $collection = $historyModel->setChanges($changes)->getChanges();

        /** @var $historyCompactModel Mage_DesignEditor_Model_History_Compact */
        $historyCompactModel = Mage::getModel('Mage_DesignEditor_Model_History_Compact');
        $historyCompactModel->compact($collection);

        $this->assertXmlStringEqualsXmlFile(
            realpath(__DIR__) . '/../_files/history/layout_renderer.xml', $historyModel->output($layoutRenderer)
        );
    }

    /**
     * Get change
     *
     * @return array
     */
    public function getChange()
    {
        return array(array(
            array(
                'handle'                => 'customer_account',
                'type'                  => 'layout',
                'element_name'          => 'customer_account_navigation',
                'action_name'           => 'move',
                'destination_container' => 'content',
                'destination_order'     => '-',
                'origin_container'      => 'top.menu',
                'origin_order'          => '-'
            ),
        ));
    }

    /**
     * Get invalid change
     *
     * @return array
     */
    public function getInvalidChange()
    {
        return array(array(
            array(
                'handle'                => 'customer_account',
                'type'                  => '',
                'element_name'          => '',
                'action_name'           => 'move',
                'destination_container' => 'content',
                'destination_order'     => '-',
                'origin_container'      => 'top.menu',
                'origin_order'          => '-'
            ),
        ));
    }

    /**
     * Get changes
     *
     * @return array
     */
    public function getChanges()
    {
        return array(array(array(
            array(
                'handle'                => 'customer_account',
                'type'                  => 'layout',
                'element_name'          => 'customer_account_navigation',
                'action_name'           => 'move',
                'destination_container' => 'content',
                'destination_order'     => '-',
                'origin_container'      => 'top.menu',
                'origin_order'          => '-'
            ),
            array(
                'handle'                => 'customer_account',
                'type'                  => 'layout',
                'element_name'          => 'customer_account_navigation',
                'action_name'           => 'move',
                'destination_container' => 'right',
                'destination_order'     => '-',
                'origin_container'      => 'content',
                'origin_order'          => '-'
            ),
            array(
                'handle'                => 'catalog_category_view',
                'type'                  => 'layout',
                'element_name'          => 'category.products',
                'action_name'           => 'move',
                'destination_container' => 'content',
                'destination_order'     => '-',
                'origin_container'      => 'right',
                'origin_order'          => '-'
            ),
            array(
                'handle'                => 'catalog_category_view',
                'type'                  => 'layout',
                'element_name'          => 'category.products',
                'action_name'           => 'remove',
            ),
        )));
    }
}

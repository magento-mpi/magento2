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

class Mage_DesignEditor_Model_HistoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_DesignEditor_Model_History::getCompactLog
     */
    public function testGetCompactLog()
    {
        $methods = array('_getManagerModel');
        /** @var $historyMock Mage_DesignEditor_Model_History */
        $historyMock = $this->getMock('Mage_DesignEditor_Model_History', $methods, array(), '', false);

        $methods = array('getHistoryLog', 'addChange');
        /** @var $managerMock Mage_DesignEditor_Model_History_Manager */
        $managerMock = $this->getMock('Mage_DesignEditor_Model_History_Manager', $methods, array(), '', false);

        $historyMock->expects($this->exactly(2))
            ->method('_getManagerModel')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->exactly(4))
            ->method('addChange')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->once())
            ->method('getHistoryLog')
            ->will($this->returnValue(array()));

        $historyMock->setChangeLog($this->_getChangeLogData())->getCompactLog();
    }

    /**
     * @covers Mage_DesignEditor_Model_History::getCompactLog
     * @expectedException Mage_DesignEditor_Exception
     */
    public function testGetCompactLogWithInvalidData()
    {
        $this->_mockTranslationHelper();

        $methods = array('_getManagerModel');
        /** @var $historyMock Mage_DesignEditor_Model_History */
        $historyMock = $this->getMock('Mage_DesignEditor_Model_History', $methods, array(), '', false);

        $methods = array('addChange');
        /** @var $managerMock Mage_DesignEditor_Model_History_Manager */
        $managerMock = $this->getMock('Mage_DesignEditor_Model_History_Manager', $methods, array(), '', false);

        $historyMock->expects($this->exactly(1))
            ->method('_getManagerModel')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->exactly(1))
            ->method('addChange')
            ->will($this->returnValue($managerMock));

        $historyMock->setChangeLog($this->_getInvalidChangeLogData())->getCompactLog();
    }

    /**
     * @covers Mage_DesignEditor_Model_History::getCompactXml
     */
    public function testGetCompactXml()
    {
        $methods = array('_getManagerModel');
        /** @var $historyMock Mage_DesignEditor_Model_History */
        $historyMock = $this->getMock('Mage_DesignEditor_Model_History', $methods, array(), '', false);

        $methods = array('getXml', 'addChange');
        /** @var $managerMock Mage_DesignEditor_Model_History_Manager */
        $managerMock = $this->getMock('Mage_DesignEditor_Model_History_Manager', $methods, array(), '', false);

        $historyMock->expects($this->exactly(2))
            ->method('_getManagerModel')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->exactly(4))
            ->method('addChange')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->once())
            ->method('getXml')
            ->will($this->returnValue(array()));

        $historyMock->setChangeLog($this->_getChangeLogData())->getCompactXml();
    }

    /**
     * @covers Mage_DesignEditor_Model_History::getCompactXml
     * @expectedException Mage_DesignEditor_Exception
     */
    public function testGetCompactXmlWithInvalidData()
    {
        $this->_mockTranslationHelper();

        $methods = array('_getManagerModel');
        /** @var $historyMock Mage_DesignEditor_Model_History */
        $historyMock = $this->getMock('Mage_DesignEditor_Model_History', $methods, array(), '', false);

        $methods = array('addChange');
        /** @var $managerMock Mage_DesignEditor_Model_History_Manager */
        $managerMock = $this->getMock('Mage_DesignEditor_Model_History_Manager', $methods, array(), '', false);

        $historyMock->expects($this->exactly(1))
            ->method('_getManagerModel')
            ->will($this->returnValue($managerMock));

        $managerMock->expects($this->exactly(1))
            ->method('addChange')
            ->will($this->returnValue($managerMock));

        $historyMock->setChangeLog($this->_getInvalidChangeLogData())->getCompactXml();
    }

    protected function _getChangeLogData()
    {
        return array(
            array(
                'handle'       => 'catalog_category_view',
                'change_type'  => 'layout',
                'element_name' => 'category.products',
                'action_name'  => 'move',
                'action_data'  => array(
                    'destination_container' => 'content',
                    'after'                 => '-',
                ),
            ),
            array(
                'handle'       => 'catalog_category_view',
                'change_type'  => 'layout',
                'element_name' => 'category.products',
                'action_name'  => 'remove',
                'action_data'  => array(),
            ),
            array(
                'handle'       => 'customer_account',
                'change_type'  => 'layout',
                'element_name' => 'customer_account_navigation',
                'action_name'  => 'move',
                'action_data'  => array(
                    'destination_container' => 'content',
                    'after'                 => '-',
                    'as'                    => 'customer_account_navigation_alias',
                ),
            ),
            array(
                'handle'       => 'customer_account',
                'change_type'  => 'layout',
                'element_name' => 'customer_account_navigation',
                'action_name'  => 'move',
                'action_data'  => array(
                    'destination_container' => 'top.menu',
                    'after'                 => '-',
                    'as'                    => 'customer_account_navigation_alias',
                ),
            ),
        );
    }

    protected function _getInvalidChangeLogData()
    {
        return array(
            array(
                'handle'       => 'catalog_category_view',
                'change_type'  => 'layout',
                'element_name' => 'category.products',
                'action_name'  => 'move',
                'action_data'  => array(
                    'destination_container' => 'content',
                    'after'                 => '-',
                ),
            ),
            array(
                'handle'       => '',
                'change_type'  => '',
                'element_name' => '',
                'action_name'  => '',
            ),
        );
    }

    /**
     * Add/remove mock for translation helper
     *
     * @param bool $add
     * @return void
     */
    protected function _mockTranslationHelper($add = true)
    {
        Mage::unregister('_helper/Mage_DesignEditor_Helper_Data');
        if ($add) {
            $helper = $this->getMock('stdClass', array('__'));
            $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
            Mage::register('_helper/Mage_DesignEditor_Helper_Data', $helper);
        }
    }
}

class Mage_DesignEditor_Model_HistoryTest_Exception extends Exception
{
}

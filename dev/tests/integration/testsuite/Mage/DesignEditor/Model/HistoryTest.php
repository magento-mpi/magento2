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

    public function setUp()
    {
        $this->_historyObject = Mage::getModel('Mage_DesignEditor_Model_History');
    }

    /**
     * @dataProvider getChangeLogData
     */
    public function testGetCompactLog($changes)
    {
        $historyObject = $this->_historyObject;
        $historyObject->setChangeLog($changes);
        $this->assertEquals($this->getCompactedChangeLogData(), $historyObject->getCompactLog());
    }

    /**
     * @dataProvider getInvalidChangeLogData
     * @expectedException Mage_DesignEditor_Exception
     */
    public function testGetCompactLogInvalidData($changes)
    {
        $historyObject = $this->_historyObject;
        $historyObject->setChangeLog($changes);
        $historyObject->getCompactLog();
    }

    /**
     * @dataProvider getChangeLogData
     */
    public function testGetCompactXml($changes)
    {
        $historyObject = $this->_historyObject;
        $historyObject->setChangeLog($changes);
        $this->assertXmlStringEqualsXmlFile(
            realpath(__DIR__) . '/../_files/history/compact_log.xml', $historyObject->getCompactXml()
        );
    }

    /**
     * @dataProvider getInvalidChangeLogData
     * @expectedException Mage_DesignEditor_Exception
     */
    public function testGetCompactXmlInvalidData($changes)
    {
        $historyObject = $this->_historyObject;
        $historyObject->setChangeLog($changes);
        $historyObject->getCompactXml();
    }

    public function getChangeLogData()
    {
        return array(array(
            array(
                array(
                    'handle'       => 'catalog_category_view',
                    'change_type'  => 'layout',
                    'element_name' => 'category.products',
                    'action_name'  => 'move',
                    'action_data'  => array(
                        'destination_container' => 'content',
                        'after'          => '-',
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
            ),
        ));
    }

    public function getCompactedChangeLogData()
    {
        return array(
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
                    'destination_container' => 'top.menu',
                    'after'                 => '-',
                    'as'                    => 'customer_account_navigation_alias',
                ),
            ),
        );
    }

    public function getInvalidChangeLogData()
    {
        return array(array(
            array(
                array(
                    'handle'       => 'catalog_category_view',
                    'change_type'  => 'layout',
                    'element_name' => 'category.products',
                    'action_name'  => 'move',
                    'action_data'  => array(
                        'destination_container' => 'content',
                        'after'          => '-',
                    ),
                ),
                array(
                    'handle'       => '',
                    'change_type'  => '',
                    'element_name' => '',
                    'action_name'  => '',
                ),
            ),
        ));
    }
}

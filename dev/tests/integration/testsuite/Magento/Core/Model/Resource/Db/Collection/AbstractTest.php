<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Db_Collection_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_model = null;

    protected function setUp()
    {
        $resourceModel = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Resource');
        $resource = $this->getMockForAbstractClass('Magento_Core_Model_Resource_Db_Abstract',
            array($resourceModel), '', true, true, true, array('getMainTable', 'getIdFieldName')
        );

        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue($resource->getTable('core_website')));
        $resource->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('website_id'));

        $fetchStrategy = $this->getMockForAbstractClass('Magento_Data_Collection_Db_FetchStrategyInterface');

        $this->_model = $this->getMockForAbstractClass(
            'Magento_Core_Model_Resource_Db_Collection_Abstract',
            array($fetchStrategy, $resource)
        );
    }

    public function testGetAllIds()
    {
        $allIds = $this->_model->getAllIds();
        sort($allIds);
        $this->assertEquals(array('0', '1'), $allIds);
    }

    public function testGetAllIdsWithBind()
    {
        $this->_model->getSelect()->where('code = :code');
        $this->_model->addBindParam('code', 'admin');
        $this->assertEquals(array('0'), $this->_model->getAllIds());
    }
}

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
     * @var \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_model = null;

    protected function setUp()
    {
        $resourceModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource');
        $resource = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb',
            array($resourceModel), '', true, true, true, array('getMainTable', 'getIdFieldName')
        );

        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue($resource->getTable('core_website')));
        $resource->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('website_id'));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');

        $eventManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Event\Manager');

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Core\Model\Resource\Db\Collection\Abstract',
            array($eventManager, $fetchStrategy, $resource)
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

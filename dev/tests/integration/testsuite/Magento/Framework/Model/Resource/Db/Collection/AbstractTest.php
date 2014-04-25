<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Db\Collection;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_model = null;

    protected function setUp()
    {
        $resourceModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\App\Resource');
        $resource = $this->getMockForAbstractClass(
            'Magento\Framework\Model\Resource\Db\AbstractDb',
            array($resourceModel),
            '',
            true,
            true,
            true,
            array('getMainTable', 'getIdFieldName')
        );

        $resource->expects(
            $this->any()
        )->method(
            'getMainTable'
        )->will(
            $this->returnValue($resource->getTable('store_website'))
        );
        $resource->expects($this->any())->method('getIdFieldName')->will($this->returnValue('website_id'));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Framework\Data\Collection\Db\FetchStrategyInterface');

        $eventManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Event\ManagerInterface'
        );

        $entityFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\EntityFactory'
        );
        $logger = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Logger');

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection',
            array($entityFactory, $logger, $fetchStrategy, $eventManager, null, $resource)
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

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Plugin;

class StoreGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Plugin\StoreGroup
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    public function setUp()
    {
        $this->_indexerMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Indexer\Stock\Processor',
            array(),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\CatalogInventory\Model\Indexer\Stock\Plugin\StoreGroup($this->_indexerMock);
    }

    /**
     * @param array $data
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave(array $data)
    {
        $subjectMock = $this->getMock('Magento\Store\Model\Resource\Group', array(), array(), '', false);
        $objectMock = $this->getMock(
            'Magento\Framework\Model\AbstractModel',
            array('getId', 'dataHasChangedFor', '__wakeup'),
            array(),
            '',
            false
        );
        $objectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($data['object_id']));
        $objectMock->expects($this->any())
            ->method('dataHasChangedFor')
            ->with('website_id')
            ->will($this->returnValue($data['has_website_id_changed']));

        $this->_indexerMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_model->beforeSave($subjectMock, $objectMock);
    }

    /**
     * @return array
     */
    public function beforeSaveDataProvider()
    {
        return array(
            array(
                array(
                    'object_id' => 1,
                    'has_website_id_changed' => true
                )
            ),
            array(
                array(
                    'object_id' => false,
                    'has_website_id_changed' => true
                )
            ),
            array(
                array(
                    'object_id' => false,
                    'has_website_id_changed' => false
                )
            ),
        );
    }
}

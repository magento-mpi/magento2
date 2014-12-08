<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Model\Resource;

class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Model\Resource\Banner
     */
    private $_resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_readAdapter;

    protected function setUp()
    {
        $select = new \Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', [], '', false));

        $writeAdapter = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\AdapterInterface',
            [],
            '',
            false,
            true,
            true,
            ['getTransactionLevel', 'fetchOne']
        );
        $writeAdapter->expects($this->once())->method('getTransactionLevel')->will($this->returnValue(0));
        $writeAdapter->expects($this->never())->method('fetchOne');

        $this->_readAdapter = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\AdapterInterface',
            [],
            '',
            false,
            true,
            true,
            ['select', 'prepareSqlCondition', 'fetchOne']
        );
        $this->_readAdapter->expects($this->once())->method('select')->will($this->returnValue($select));

        $this->_resource = $this->getMock(
            'Magento\Framework\App\Resource',
            [],
            [],
            '',
            false
        );
        $this->_resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));
        $this->_resource->expects(
            $this->exactly(2)
        )->method(
            'getConnection'
        )->with()->will(
            $this->returnValueMap([['core_write', $writeAdapter], ['core_read', $this->_readAdapter]])
        );

        $this->_eventManager = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            ['dispatch'],
            [],
            '',
            false
        );

        $this->_bannerConfig = $this->getMock(
            'Magento\Banner\Model\Config',
            ['explodeTypes'],
            [],
            '',
            false
        );

        $salesruleColFactory = $this->getMock(
            'Magento\Banner\Model\Resource\Salesrule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $catRuleColFactory = $this->getMock(
            'Magento\Banner\Model\Resource\Catalogrule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_resourceModel = new \Magento\Banner\Model\Resource\Banner(
            $this->_resource,
            $this->_eventManager,
            $this->_bannerConfig,
            $salesruleColFactory,
            $catRuleColFactory
        );
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
        $this->_resource = null;
        $this->_eventManager = null;
        $this->_bannerConfig = null;
        $this->_readAdapter = null;
    }

    public function testGetStoreContent()
    {
        $this->_readAdapter->expects(
            $this->once()
        )->method(
            'fetchOne'
        )->with(
            $this->logicalAnd(
                $this->isInstanceOf('Zend_Db_Select'),
                $this->equalTo(
                    'SELECT "main_table"."banner_content"' .
                    ' FROM "magento_banner_content" AS "main_table"' .
                    ' WHERE (main_table.banner_id = 123) AND (main_table.store_id IN (5, 0))' .
                    ' ORDER BY "main_table"."store_id" DESC'
                )
            )
        )->will(
            $this->returnValue('Banner Contents')
        );

        $this->_eventManager->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            'magento_banner_resource_banner_content_select_init',
            $this->arrayHasKey('select')
        );

        $this->assertEquals('Banner Contents', $this->_resourceModel->getStoreContent(123, 5));
    }

    public function testGetStoreContentFilterByTypes()
    {
        $bannerTypes = ['content', 'footer', 'header'];
        $this->_bannerConfig->expects(
            $this->once()
        )->method(
            'explodeTypes'
        )->with(
            $bannerTypes
        )->will(
            $this->returnValue(['footer', 'header'])
        );
        $this->_resourceModel->filterByTypes($bannerTypes);

        $this->_readAdapter->expects(
            $this->exactly(2)
        )->method(
            'prepareSqlCondition'
        )->will(
            $this->returnValueMap(
                [
                    ['banner.types', ['finset' => 'footer'], 'banner.types IN ("footer")'],
                    ['banner.types', ['finset' => 'header'], 'banner.types IN ("header")'],
                ]
            )
        );
        $this->_readAdapter->expects(
            $this->once()
        )->method(
            'fetchOne'
        )->with(
            $this->logicalAnd(
                $this->isInstanceOf('Zend_Db_Select'),
                $this->equalTo(
                    'SELECT "main_table"."banner_content", "banner".*' .
                    ' FROM "magento_banner_content" AS "main_table"' .
                    "\n" .
                    ' INNER JOIN "magento_banner" AS "banner"' .
                    ' ON main_table.banner_id = banner.banner_id' .
                    ' WHERE' .
                    ' (main_table.banner_id = 123)' .
                    ' AND (main_table.store_id IN (5, 0))' .
                    ' AND (banner.types IN ("footer") OR banner.types IN ("header"))' .
                    ' ORDER BY "main_table"."store_id" DESC'
                )
            )
        )->will(
            $this->returnValue('Banner Contents')
        );

        $this->_eventManager->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            'magento_banner_resource_banner_content_select_init',
            $this->arrayHasKey('select')
        );

        $this->assertEquals('Banner Contents', $this->_resourceModel->getStoreContent(123, 5));
    }
}

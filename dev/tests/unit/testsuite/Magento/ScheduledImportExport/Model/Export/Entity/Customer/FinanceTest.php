<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Export\Entity\Customer;

class FinanceTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Test attribute code and website specific attribute code
     */
    const ATTRIBUTE_CODE = 'code1';

    const WEBSITE_ATTRIBUTE_CODE = 'website1_code1';

    /**#@-*/

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID => 'admin', 1 => 'website1');

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = array(array('attribute_id' => 1, 'attribute_code' => self::ATTRIBUTE_CODE));

    /**
     * Customer data
     *
     * @var array
     */
    protected $_customerData = array(
        'website_id' => 1,
        'email' => '@email@domain.com',
        self::WEBSITE_ATTRIBUTE_CODE => 1
    );

    /**
     * Customer financial data export model
     *
     * @var \Magento\ScheduledImportExport\Model\Export\Entity\Customer\Finance
     */
    protected $_model;

    protected function setUp()
    {
        $scopeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $customerCollectionFactory = $this->getMock(
            'Magento\ScheduledImportExport\Model\Resource\Customer\CollectionFactory',
            array(),
            array(),
            '',
            false,
            false
        );

        $eavCustomerFactory = $this->getMock(
            'Magento\ImportExport\Model\Export\Entity\Eav\CustomerFactory',
            array(),
            array(),
            '',
            false,
            false
        );

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $storeManager->expects(
            $this->exactly(2)
        )->method(
            'getWebsites'
        )->will(
            $this->returnCallback(array($this, 'getWebsites'))
        );

        $this->_model = new \Magento\ScheduledImportExport\Model\Export\Entity\Customer\Finance(
            $scopeConfig,
            $storeManager,
            $this->getMock('Magento\ImportExport\Model\Export\Factory', array(), array(), '', false, false),
            $this->getMock(
                'Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory',
                array(),
                array(),
                '',
                false,
                false
            ),
            $customerCollectionFactory,
            $eavCustomerFactory,
            $this->getMock('Magento\ScheduledImportExport\Helper\Data', array(), array(), '', false, false),
            $this->_getModelDependencies()
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $translator = $this->getMock('stdClass');

        /** @var $attributeCollection \Magento\Data\Collection|PHPUnit_Framework_TestCase */
        $attributeCollection = $this->getMock(
            'Magento\Data\Collection',
            array('getEntityTypeCode'),
            array($this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false))
        );
        foreach ($this->_attributes as $attributeData) {
            $arguments = $objectManagerHelper->getConstructArguments(
                'Magento\Eav\Model\Entity\Attribute\AbstractAttribute'
            );
            $arguments['data'] = $attributeData;
            $attribute = $this->getMockBuilder(
                'Magento\Eav\Model\Entity\Attribute\AbstractAttribute'
            )->setConstructorArgs(
                $arguments
            )->setMethods(
                array('_construct')
            )->getMock();
            $attributeCollection->addItem($attribute);
        }

        $data = array(
            'translator' => $translator,
            'attribute_collection' => $attributeCollection,
            'page_size' => 1,
            'collection_by_pages_iterator' => 'not_used',
            'entity_type_id' => 1,
            'customer_collection' => 'not_used',
            'customer_entity' => 'not_used',
            'module_helper' => 'not_used'
        );

        return $data;
    }

    /**
     * Get websites stub
     *
     * @param bool $withDefault
     * @return array
     */
    public function getWebsites($withDefault = false)
    {
        $websites = array();
        if (!$withDefault) {
            unset($websites[0]);
        }
        foreach ($this->_websites as $id => $code) {
            if (!$withDefault && $id == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                continue;
            }
            $websiteData = array('id' => $id, 'code' => $code);
            $websites[$id] = new \Magento\Object($websiteData);
        }

        return $websites;
    }

    /**
     * Test for method exportItem()
     *
     * @covers \Magento\ScheduledImportExport\Model\Export\Entity\Customer\Finance::exportItem
     */
    public function testExportItem()
    {
        $writer = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Export\Adapter\AbstractAdapter',
            array(),
            '',
            false,
            false,
            true,
            array('writeRow')
        );

        $writer->expects(
            $this->once()
        )->method(
            'writeRow'
        )->will(
            $this->returnCallback(array($this, 'validateWriteRow'))
        );

        $this->_model->setWriter($writer);

        $item = $this->getMockForAbstractClass(
            'Magento\Model\AbstractModel',
            array(),
            '',
            false,
            false,
            true,
            array('__wakeup')
        );
        /** @var $item \Magento\Model\AbstractModel */
        $item->setData($this->_customerData);

        $this->_model->exportItem($item);
    }

    /**
     * Validate data passed to writer's writeRow() method
     *
     * @param array $row
     */
    public function validateWriteRow(array $row)
    {
        $emailColumn = Finance::COLUMN_EMAIL;
        $this->assertEquals($this->_customerData['email'], $row[$emailColumn]);

        $websiteColumn = Finance::COLUMN_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$websiteColumn]);

        $financeWebsiteCol = Finance::COLUMN_FINANCE_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$financeWebsiteCol]);

        $this->assertEquals($this->_customerData[self::WEBSITE_ATTRIBUTE_CODE], $row[self::ATTRIBUTE_CODE]);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     <package-name>
 * @subpackage  <subpackage-name>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Import Data resource model
 *
 * @magentoDataFixture Magento/ImportExport/_files/import_data.php
 */
namespace Magento\ImportExport\Model\Resource\Import;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Resource\Import\Data
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model = \Mage::getResourceModel('Magento\ImportExport\Model\Resource\Import\Data');
    }

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is unique
     */
    public function testGetUniqueColumnData()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ImportExport\Import\Data');

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getUniqueColumnData('entity'));
    }

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is NOT unique
     *
     * @expectedException \Magento\Core\Exception
     */
    public function testGetUniqueColumnDataException()
    {
        $this->_model->getUniqueColumnData('data');
    }

    /**
     * Test successful getBehavior()
     */
    public function testGetBehavior()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ImportExport\Import\Data');

        $this->assertEquals($expectedBunches[0]['behavior'], $this->_model->getBehavior());
    }

    /**
     * Test successful getEntityTypeCode()
     */
    public function testGetEntityTypeCode()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ImportExport\Import\Data');

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getEntityTypeCode());
    }
}

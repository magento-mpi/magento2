<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\EntityAbstract
 */
namespace Magento\ImportExport\Model\Import;

class EntityAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     */
    public function testSaveValidatedBunches()
    {
        $source = new \Magento\ImportExport\Model\Import\Source\Csv(
            __DIR__ . '/Entity/Eav/_files/customers_for_validation_test.csv'
        );
        $source->rewind();
        $expected = $source->current();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $coreData = $objectManager->get('Magento\Core\Helper\Data');
        $coreString = $objectManager->get('Magento\Core\Helper\String');
        $storeConfig = $objectManager->get('Magento\Core\Model\Store\Config');
        
        /** @var $model \Magento\ImportExport\Model\Import\EntityAbstract|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\EntityAbstract', array(
            $coreData, $coreString, $storeConfig
        ));
        $model->expects($this->any())
            ->method('validateRow')
            ->will($this->returnValue(true));
        $model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue('customer'));

        $model->setSource($source);

        $method = new \ReflectionMethod($model, '_saveValidatedBunches');
        $method->setAccessible(true);
        $method->invoke($model);

        $dataSourceModel = \Magento\ImportExport\Model\Import::getDataSourceModel();
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}

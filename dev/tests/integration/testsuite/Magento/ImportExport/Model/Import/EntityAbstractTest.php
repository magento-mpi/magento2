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
 * Test class for \Magento\ImportExport\Model\Import\AbstractEntity
 */
namespace Magento\ImportExport\Model\Import;

class EntityAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     */
    public function testSaveValidatedBunches()
    {
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\App\Filesystem');
        $directory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $source = new \Magento\ImportExport\Model\Import\Source\Csv(
            __DIR__ . '/Entity/_files/customers_for_validation_test.csv',
            $directory
        );
        $source->rewind();
        $expected = $source->current();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $model \Magento\ImportExport\Model\Import\AbstractEntity|\PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Import\AbstractEntity',
            array(
                $objectManager->get('Magento\Core\Helper\Data'),
                $objectManager->get('Magento\Stdlib\String'),
                $objectManager->get('Magento\App\Config\ScopeConfigInterface'),
                $objectManager->get('Magento\ImportExport\Model\ImportFactory'),
                $objectManager->get('Magento\ImportExport\Model\Resource\Helper'),
                $objectManager->get('Magento\App\Resource')
            )
        );
        $model->expects($this->any())->method('validateRow')->will($this->returnValue(true));
        $model->expects($this->any())->method('getEntityTypeCode')->will($this->returnValue('customer'));

        $model->setSource($source);

        $method = new \ReflectionMethod($model, '_saveValidatedBunches');
        $method->setAccessible(true);
        $method->invoke($model);

        /** @var $dataSourceModel \Magento\ImportExport\Model\Resource\Import\Data */
        $dataSourceModel = $objectManager->get('Magento\ImportExport\Model\Resource\Import\Data');
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}

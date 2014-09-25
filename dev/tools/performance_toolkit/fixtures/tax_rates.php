<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$this->resetObjectManager();
/** Clean predefined tax rates to maintain consistency */
/** @var $collection Magento\Tax\Model\Resource\Calculation\Rate\Collection */
$collection = $this->getObjectManager()->get('Magento\Tax\Model\Resource\Calculation\Rate\Collection');

/** @var $model Magento\Tax\Model\Calculation\Rate */
$model = $this->getObjectManager()->get('Magento\Tax\Model\Calculation\Rate');

foreach ($collection->getAllIds() as $id) {
    $model->setId($id);
    $model->delete();
}
/**
 * Import tax rates with import handler
 */
$filename = realpath(__DIR__ . '/tax_rates.csv');
$file = array (
    'name' => $filename,
    'type' => 'application/vnd.ms-excel',
    'tmp_name' => $filename,
    'error' => 0,
    'size' => filesize($filename),
);
$importHandler = $this->getObjectManager()->create('Magento\TaxImportExport\Model\Rate\CsvImportHandler');
$importHandler->importFromCsvFile($file);

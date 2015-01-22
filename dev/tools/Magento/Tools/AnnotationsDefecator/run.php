<?php
/**
 * Created by PhpStorm.
 * User: dkvashnin
 * Date: 10/26/14
 * Time: 9:35 PM
 */
namespace Magento\Tools\AnnotationsDefecator;
require __DIR__ . '/../../../bootstrap.php';


/**
$file = '/var/www/html/dkvashnin/magento2/magento2/app/code/Magento/Paypal/Model/Api/Nvp.php';
$tmpFile = __DIR__ . '/tml.php';

$fileItemFactory = new FileItemFactory;
$fileItem = $fileItemFactory->create($file);
$annotation = $fileItem->getStructureHasLineNumber(2);
if ($annotation instanceof Annotation) {
    $annotation->addContent('HAHA');
}

$line = $fileItem->getStructureHasLineNumber(17);
if ($line instanceof Line) {
    $annotation = new Annotation();
    $annotation->setContentIndent(Line::getContentIndent($line->getContent()));
    $annotation->addContent('Suck ass');
    $fileItem->appendItemBeforeExistingItem($annotation, $line);
}

$fileItem->reindexContentStructure();
$fileContent = $fileItem->getContentArray();


$fh = fopen($tmpFile, 'a');
foreach ($fileContent as $line) {
    fwrite($fh, $line . PHP_EOL);
}
fclose($fh);
*/

$report = '/home/dkvashnin/livecode/phpcs_report.xml';
/**$aggregatedData = getReportAggregatedArray($report); */
$processor = new PhpCsProcessor();
$processor->run($report);

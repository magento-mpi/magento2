<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\AnnotationsDefecator;


class PhpCsProcessor
{
    /**
     * Run annotations processor to insert suppress warnings annotations
     *
     * @param string $reportPath Path to phpmd report
     */
    public function run($reportPath)
    {
        foreach ($this->getReportAggregatedArray($reportPath) as $fileName) {
            $fileItemFactory = new FileItemFactory;
            $fileItem = $fileItemFactory->create($fileName);
            
            if (strpos($fileItem->getContent(), '@codingStandardsIgnoreFile') !== false) {
                echo '[S] Already ignored' . $fileName . PHP_EOL;
                continue;
            }
            $phpTag = $fileItem->getStructureHasLineNumber(0);
            if (strpos($phpTag->getContent(), '<?php') === false) {
                echo '[S] No php open tag in ' . $fileName . PHP_EOL;
                continue;
            }

            $copyrightAnnotation = $fileItem->getStructureHasLineNumber(1);
            if (!$copyrightAnnotation instanceof Annotation) {
                echo '[S] No Copyright annotation ' . $fileName . PHP_EOL;
                continue;
            }
       
            $copyrightStart = 1;
            while (true) {
                $copyrightStart++;
                $afterCopyrightItem = $fileItem->getStructureHasLineNumber($copyrightStart);
                if ($afterCopyrightItem !== $copyrightAnnotation) {
                    break;
                }
            }
            
            $codingStandardsIgnoreFileLine = new Line('// @codingStandardsIgnoreFile', 0);
            $emptyLine = new Line('', 0);

            $fileItem->appendItemBeforeExistingItem($emptyLine, $afterCopyrightItem);
            $fileItem->appendItemBeforeExistingItem($codingStandardsIgnoreFileLine, $afterCopyrightItem);

            if ($afterCopyrightItem->getContent() != '') {
                $fileItem->appendItemBeforeExistingItem($emptyLine, $afterCopyrightItem);
            }

            $fileItem->reindexContentStructure();
            $this->deleteFile($fileName);
            $this->saveFile($fileName, $fileItem);
        }
    }

    /**
     * Saves file with new content
     *
     * @param string $fileName
     * @param FileItem $fileItem
     */
    private function saveFile($fileName, FileItem $fileItem)
    {
        $fileContent = $fileItem->getContentArray();

        $fh = fopen($fileName, 'a');
        foreach ($fileContent as $line) {
            fwrite($fh, $line . PHP_EOL);
        }
        fclose($fh);
    }

    /**
     * Deletes file
     *
     * @param string $path
     */
    private function deleteFile($path)
    {
        unlink($path);
    }

    /**
     * Returns report data as aggregated array
     * 'file name'::string => [
     *      'violation begin line'::integer => [
     *          'violation index'::integer => [
     *              'rule' => 'rule class name'::string,
     *              'comment' => 'comment about violation'::string
     *          ]
     *      ]
     * ]
     *
     * @param string $reportPath Absolute path to report.xml
     * @returns array
     */
    public function getReportAggregatedArray($reportPath)
    {
        $reportObj = simplexml_load_file($reportPath);
        $aggregatedData = [];
        /** @var \SimpleXMLElement $fileViolation */
        foreach ($reportObj->children() as $fileViolation) {
            $fileName = (string)$fileViolation->attributes()['name'];
            if (!is_file($fileName)) {
                continue;
            }
            $aggregatedData[] = $fileName;
        }

        return array_unique($aggregatedData);
    }
}

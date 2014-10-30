<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator;

class Processor
{
    /**
     * Run annotations processor to insert suppress warnings annotations
     *
     * @param string $reportPath Path to phpmd report
     */
    public function run($reportPath)
    {
        foreach ($this->getReportAggregatedArray($reportPath) as $fileName => $violations) {
            $fileItemFactory = new FileItemFactory;
            $fileItem = $fileItemFactory->create($fileName);

            foreach ($violations as $violationLine => $rules) {
                $annotation = $this->getAnnotationForLine($fileItem, $violationLine);
                foreach ($rules as $rule) {
                    $suppressStatement = sprintf('@SuppressWarnings(PHPMD.%s)', $rule);
                    if (strpos($annotation->getContent(), $suppressStatement) === false) {
                        $annotation->addContent($suppressStatement);
                    }
                }
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
     * Returns Annotation object for passed line
     *
     * @param FileItem $fileItem
     * @param int $line
     *
     * @returns Annotation
     */
    private function getAnnotationForLine($fileItem, $line)
    {
        $lineItem = $fileItem->getStructureHasLineNumber($this->convertLineNumber($line));
        if (!$lineItem instanceof Line\FunctionClassItem) {
            $functionClassLine = $line - 1;
            while ($functionClassLine) {
                $lineItem = $fileItem->getStructureHasLineNumber($this->convertLineNumber($functionClassLine));
                if ($lineItem instanceof Line\FunctionClassItem) {
                    $line = $functionClassLine;
                    break;
                }
                $functionClassLine--;
            }
            if (!$functionClassLine) {
                echo 'NOT FOUND function class etc: ' . $fileItem->getFileName() . ' . Line: ' . $line . PHP_EOL;
            }
        }

        // get previous element for given line
        $item = $fileItem->getStructureHasLineNumber($this->convertLineNumber($line) - 1);
        if ($item instanceof Annotation) {
            return $item;
        }

        $annotation = new Annotation();
        $item = $fileItem->getStructureHasLineNumber($this->convertLineNumber($line));
        $annotation->setContentIndent($this->getIndentForLine($fileItem, $line) + 1);
        $fileItem->appendItemBeforeExistingItem($annotation, $item);

        return $annotation;
    }

    /**
     * @param FileItem $fileItem
     * @param int $line
     * @return int
     */
    private function getIndentForLine($fileItem, $line)
    {
        $item = $fileItem->getStructureHasLineNumber($this->convertLineNumber($line));

        if ($item instanceof Line) {
            return Line::getContentIndent($item->getContent());
        }

        $item = $fileItem->getStructureHasLineNumber($this->convertLineNumber($line) - 1);

        return Line::getContentIndent($item->getContent());
    }

    /**
     * Converts real file number to array number
     *
     * @param int $realNumber
     * @return int
     */
    private function convertLineNumber($realNumber)
    {
        return $realNumber - 1;
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
            $aggregatedData[$fileName] = [];

            /** @var \SimpleXMLElement $violation */
            foreach ($fileViolation as $violation) {
                $startLine = (string)$violation->attributes()['beginline'];
                if (!isset($aggregatedData[$fileName][$startLine])) {
                    $aggregatedData[$fileName][$startLine] = [];
                }
                $rule = $this->_getRuleName($violation);
                if (!in_array($rule, $aggregatedData[$fileName][$startLine])) {
                    $aggregatedData[$fileName][$startLine][] = $rule;
                }

            }
        }
        return $aggregatedData;
    }

    /**
     * As PHPMD is buggy and can fill rule with nonce, we need to check externalInfoUrl also
     *
     * @param \SimpleXMLElement $violation
     * @return string
     */
    private function _getRuleName(\SimpleXMLElement $violation)
    {
        $rule = (string)$violation->attributes()['rule'];
        $externalInfoUrl = (string)$violation->attributes()['externalInfoUrl'];
        $rules = [
            'CyclomaticComplexity',
            'NPathComplexity',
            'ExcessiveMethodLength',
            'ExcessiveClassLength',
            'ExcessiveParameterList',
            'ExcessivePublicCount',
            'TooManyFields',
            'TooManyMethods',
            'ExcessiveClassComplexity',

            'ExitExpression',
            'EvalExpression',
            'GotoStatement',
            'NumberOfChildren',
            'DepthOfInheritance',
            'CouplingBetweenObjects',

            'ShortVariable',
            'LongVariable',
            'ShortMethodName',
            'ConstantNamingConventions',
            'BooleanGetMethodName',

            'UnusedPrivateField',
            'UnusedLocalVariable',
            'UnusedPrivateMethod',
            'UnusedFormalParameter'
        ];

        if (in_array($rule, $rules)) {
            return $rule;
        }

        foreach ($rules as $rule) {
            if (strpos($externalInfoUrl, strtolower($rule))) {
                return $rule;
            }
        }

        return $rule;
    }
}

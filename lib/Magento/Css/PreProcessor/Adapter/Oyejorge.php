<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Adapter;

if (!class_exists('Less_Parser')) {
    require_once __DIR__ . '/../../../../../lib/oyejorge/phpless/phpless/Less.php';
}

/**
 * Oyejorge adapter model
 */
class Oyejorge implements \Magento\Css\PreProcessor\AdapterInterface
{
    /**
     * @param string $sourceFilePath
     * @return string
     */
    public function process($sourceFilePath)
    {
        $parser = new \Less_Parser();
        $parser->parseFile($sourceFilePath, '');
        return $parser->getCss();
    }
}

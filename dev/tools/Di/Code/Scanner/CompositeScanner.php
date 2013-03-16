<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class CompositeScanner implements ScannerInterface
{
    /**
     * @var ScannerInterface[]
     */
    protected $_children = array();

    /**
     * Add child scanner
     *
     * @param ScannerInterface $scanner
     * @param string $type
     */
    public function addChild(ScannerInterface $scanner, $type)
    {
        $this->_children[$type] = $scanner;
    }

    /**
     * Scan files
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = array();
        foreach ($this->_children as $type => $scanner) {
            $output = array_merge($output, $scanner->collectEntities($files[$type]));
        }
        $output = array_unique($output);
        return $output;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class Composite
{
    /**
     * @var ScannerInterface[]
     */
    protected $_children;

    public function addChild(ScannerInterface $scanner)
    {
        $this->_children[] = $scanner;
    }

    /**
     * @return array
     */
    public function collectEntities()
    {
        $output = array();
        foreach ($this->_children as $scanner) {
            $output = array_merge($output, $scanner->collectEntities());
        }
        return $output;
    }
}
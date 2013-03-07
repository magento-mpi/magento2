<?php

class Magento_Code_ScannerComposite
{
    /**
     * @var Magento_Code_ScannerInterface[]
     */
    protected $_children;

    public function addChild(Magento_Code_ScannerInterface $scanner)
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
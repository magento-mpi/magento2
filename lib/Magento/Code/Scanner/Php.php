<?php

class Magento_Code_Scanner_Php implements Magento_Code_ScannerInterface
{
    /**
     * @var Zend\Code\Scanner\AggregateDirectoryScanner
     */
    protected $_scanner;

    /**
     * @var string
     */
    protected $_pattern;

    /**
     * @param Zend\Code\Scanner\AggregateDirectoryScanner $scanner
     * @param $pattern
     */
    public function __construct(Zend\Code\Scanner\AggregateDirectoryScanner $scanner, $pattern)
    {
        $this->_scanner = $scanner;
        $this->_pattern = $pattern;
    }

    /**
     * @return array
     */
    public function collectEntities()
    {
        $output = array();
        foreach ($this->_scanner->getFiles() as $file) {
            $content = file_get_contents($file);
            $matches = array();
            if(preg_match_all($this->_pattern, $content, $matches)) {
                $output = array_merge($output, $matches[0]);
            }
        }
        $output = array_unique($output);
        sort($output);
        return $output;
    }

}

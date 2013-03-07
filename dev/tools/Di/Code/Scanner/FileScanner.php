<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class FileScanner
{
    /**
     * @var array
     */
    protected $_files;

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern;

    /**
     * @param array $files
     * @param $pattern
     */
    public function __construct(array $files, $pattern)
    {
        $this->_files = $files;
        $this->_pattern = $pattern;
    }

    /**
     * Get array of class names
     *
     * @return array
     */
    public function collectEntities()
    {
        $output = array();
        foreach ($this->_files as $file) {
            $content = file_get_contents($file);
            $matches = array();
            if(preg_match_all($this->_pattern, $content, $matches)) {
                $output = array_merge($output, $matches[1]);
            }
        }
        $output = array_unique($output);
        sort($output);
        return $output;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class FileScanner implements ScannerInterface
{
    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern;

    /**
     * Get array of class names
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = array();
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $content = $this->_prepareContent($content);
            $matches = array();
            if (preg_match_all($this->_pattern, $content, $matches)) {
                $output = array_merge($output, $matches[1]);
            }
        }
        $output = array_unique($output);
        $output = $this->_filterEntities($output);
        return $output;
    }

    /**
     * Prepare file content
     *
     * @param string $content
     * @return string
     */
    protected function _prepareContent($content)
    {
        return $content;
    }

    /**
     * Filter found entities if needed
     *
     * @param array $output
     * @return array
     */
    protected function _filterEntities(array $output)
    {
        return $output;
    }
}

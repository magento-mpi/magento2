<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration XML-files merger
 */
abstract class Magento_Config_XmlAbstract
{
    /**
     * Data extracted from the merged configuration files
     *
     * @var array
     */
    protected $_data;

    /**
     * Instantiate with the list of files to merge
     *
     * @param string|array $configDataOrFiles
     * @throws InvalidArgumentException
     */
    public function __construct($configDataOrFiles)
    {
        if (is_string($configDataOrFiles)) {
            $this->_importData($configDataOrFiles);
        } else if (is_array($configDataOrFiles)) {
            if (empty($configDataOrFiles)) {
                throw new InvalidArgumentException('There must be at least one configuration file specified.');
            }
            $this->_data = $this->_extractData($this->_merge($configDataOrFiles));
        } else {
            throw new InvalidArgumentException('Configuration data or list of configuration files is expected.');
        }
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    abstract public function getSchemaFile();

    /**
     * Export configuration data in a format suitable for permanent storage
     *
     * @return string
     */
    public function exportData()
    {
        return serialize($this->_data);
    }

    /**
     * Import configuration data from the permanent storage format
     *
     * @param string $data
     */
    protected function _importData($data)
    {
        $this->_data = unserialize($data);
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    abstract protected function _extractData(DOMDocument $dom);

    /**
     * Merge the config XML-files
     *
     * @param array $configFiles
     * @return DOMDocument
     * @throws Magento_Exception if a non-existing or invalid XML-file passed
     */
    protected function _merge($configFiles)
    {
        $domConfig = new Magento_Config_Dom($this->_getInitialXml(), $this->_getIdAttributes());
        foreach ($configFiles as $file) {
            if (!file_exists($file)) {
                throw new Magento_Exception("File does not exist: {$file}");
            }
            $domConfig->merge(file_get_contents($file));
            if (!$domConfig->validate($this->getSchemaFile(), $errors)) {
                $message = "Invalid XML-file: {$file}\n";
                /** @var libXMLError $error */
                foreach ($errors as $error) {
                    $message .= "{$error->message} Line: {$error->line}\n";
                }
                throw new Magento_Exception($message);
            }
        }
        return $domConfig->getDom();
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    abstract protected function _getInitialXml();

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    abstract protected function _getIdAttributes();
}

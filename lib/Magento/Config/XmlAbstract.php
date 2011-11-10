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
     * @var DOMDocument
     */
    protected $_dom = null;

    /**
     * Instantiate with the list of files to merge
     *
     * @param array $configFiles
     * @throws Exception
     */
    public function __construct(array $configFiles)
    {
        if (empty($configFiles)) {
            throw new Exception('There must be at least one configuration file specified.');
        }
        $this->_merge($configFiles);
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    abstract public function getSchemaFile();

    /**
     * Merge the config XML-files
     *
     * @param array $configFiles
     * @throws Exception if a non-existing or invalid XML-file passed
     */
    protected function _merge($configFiles)
    {
        $domConfig = new Magento_Config_Dom($this->_getInitialXml(), $this->_getIdAttributes());
        foreach ($configFiles as $file) {
            if (!file_exists($file)) {
                throw new Exception("File does not exist: {$file}");
            }
            $domConfig->merge(file_get_contents($file));
            if (!$domConfig->validate($this->getSchemaFile(), $errors)) {
                $message = "Invalid XML-file: {$file}\n";
                /** @var libXMLError $error */
                foreach ($errors as $error) {
                    $message .= "{$error->message} Line: {$error->line}\n";
                }
                throw new Exception($message);
            }
        }
        $this->_dom = $domConfig->getDom();
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

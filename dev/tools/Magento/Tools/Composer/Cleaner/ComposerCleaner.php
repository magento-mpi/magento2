<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Cleaner;

/**
 * Helper class for Removing composer.json files
 */
class ComposerCleaner
{

    /**
     * Collection of Components
     *
     * @var string
     */
    private $_components;

    /**
     * Application Logger
     *
     * @var \Zend_Log
     */
    private $_logger;

    /**
     * Root Directory Location
     *
     * @var string
     */
    private $_rootDir;

    /**
     * Composer Cleaner Construct
     *
     * @param string $rootDir
     * @param array $components
     * @param \Zend_Log $logger
     */
    public function __construct($rootDir, array $components, \Zend_Log $logger)
    {
        $this->_components = $components;
        $this->_logger = $logger;
        $this->_rootDir = $rootDir;
    }

    /**
     * Cleans all composer.json for each component
     *
     * @return int
     */
    public function clean()
    {
        /**
         * @var $component \Magento\Tools\Composer\Model\Package
         */
        foreach ($this->_components as  $component) {
            $fileLocation = $this->_rootDir . $component->getLocation() . "/composer.json";
            if (file_exists($fileLocation)) {
                unlink($fileLocation);
                $this->_logger->debug(sprintf("Cleared composer.json on %-40s", $component->getName()));
            } else {
                $this->_logger->debug(sprintf("Skipped. composer.json doesn't exist for %s", $component->getName()));
            }
        }
        return sizeof($this->_components);
    }
}
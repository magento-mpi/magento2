<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Creator;

use \Magento\Tools\Composer\Model\Package;

/**
 * Create Composer Class
 */
class ComposerCreator
{

    /**
     * Application Logger
     *
     * @var \Zend_Log
     */
    private $_logger;

    /**
     * Root Directory
     * @var string
     */
    private $_rootDir;

    /**
     * Composer Creator Constructor
     *
     * @param string $rootDir
     * @param \Zend_Log $logger
     */
    public function __construct($rootDir, \Zend_Log $logger)
    {
        $this->_rootDir = $rootDir;
        $this->_logger = $logger;
    }

    /**
     * Creates composer.json for components
     * @param array $components
     * @return int
     */
    public function create(array $components)
    {
        $counter = 0;
        foreach ($components as $component) {
            /** @var Package $component */
            $command = 'cd ' . $this->_rootDir . $component->getLocation() . ' && php ' .
                __DIR__ . '/../composer.phar init '.
                '--name "' . strtolower($component->getName()) .
                '" --description="N/A" ' .
                '-n';
            //Command to include package installer.
            $dependencies = $component->getDependencies();

            /** @var Package  $dependency */
            foreach ($dependencies as $dependency) {
                $command .= ' --require="' . strtolower($dependency->getName()) . ':'.$dependency->getVersion() . '"';
            }
            $command .= ' --require="magento/framework:0.1.0"';
            $output = array();
            exec($command, $output);
            if (sizeof($output) > 0 ) {
                //Failed
                $this->_logger->error(implode(". ", $output));
            } else {
                //Success
                $this->addAdditionalInfo($component);
                $counter++;
                $this->_logger->debug(sprintf("Created composer.json for %-40s [%7s]",
                    $component->getName(), $component->getVersion()));
            }

        }
        return $counter;
    }

    /**
     * Adds version and type information to composer.json file
     *
     * @param Package $component
     */
    private function addAdditionalInfo(Package $component)
    {
        if ($component->getVersion() != null && $component->getVersion() != "") {
            $json = file_get_contents( $this->_rootDir . $component->getLocation() . '/composer.json');
            $composer = json_decode($json, true);
            if (!array_key_exists('type', $composer)) {
                $composer['type'] = $component->getType();
            }
            if (!array_key_exists('version', $composer)) {
                $composer['version'] = $component->getVersion();
            }
            file_put_contents( $this->_rootDir . $component->getLocation() . '/composer.json',
                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
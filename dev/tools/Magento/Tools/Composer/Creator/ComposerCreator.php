<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Creator;

use \Magento\Tools\Composer\CreatorInterface;
use \Magento\Tools\Composer\Model\Package;

/**
 * Create Composer Class
 */
class ComposerCreator implements CreatorInterface
{

    /**
     * List of Components
     *
     * @var array
     */
    private $_components;

    /**
     * Application Logger
     *
     * @var \Zend_Log
     */
    private $_logger;

    /**
     * Composer Creator Constructor
     *
     * @param array $components
     * @param \Zend_Log $logger
     */
    public function __construct($components, \Zend_Log $logger)
    {
        $this->_components = $components;
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $counter = 0;
        foreach ($this->_components as $component) {
            $command = "cd ".$component->getLocation() ." && php ".__DIR__."/../composer.phar init  --name \"".
                        $component->getName(). "\" --description=\"We would be updating the description soon.\" ".
                        "--author=\"Magento Support <support@magentocommerce.com>\" --stability=\"dev\" -n";
            //Command to include package installer.
            $dependencies = $component->getDependencies();
            foreach ($dependencies as $dependency) {
                 $command .= " --require=\"" . $dependency->getName().":".$dependency->getVersion()."\" ";
            }
            $command .= " --require=\"Magento/Package-installer:*\"";
            $output = array();
            exec($command, $output);
            if (sizeof($output) > 0 ) {
                //Failed
                print_r($output);
            } else {
                //Success
                $this->addVersionandTypeInfo($component);
                $counter++;
                $this->_logger->log(sprintf("Created composer.json for %-40s [%7s]",
                    $component->getName(), $component->getVersion()), \Zend_Log::DEBUG);
            }

        }
        return $counter;
    }

    /**
     * Adds version and type information to composer.json file
     *
     * @param Package $component
     */
    public function addVersionandTypeInfo(Package $component)
    {
        if ($component->getVersion() != null && $component->getVersion() != "") {
            $json = file_get_contents($component->getLocation()."/composer.json");
            $composer = json_decode($json, true);
            if (!array_key_exists("type", $composer)) {
                $composer["type"] = $component->getType();
            }
            if (!array_key_exists("version", $composer)) {
                $composer["version"] = $component->getVersion();
            }
            file_put_contents($component->getLocation()."/composer.json",
                                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
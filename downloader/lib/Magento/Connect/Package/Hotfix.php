<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Class to work with Magento Connect Hotfix
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Connect\Package;

class Hotfix extends \Magento\Connect\Package
{

    /**
     * Initializes an empty package object
     *
     * @param null|string $definition optional package definition xml
     * @return \Magento\Connect\Package
     */
    protected function _init($definition=null)
    {

        if (!is_null($definition)) {
            $this->_packageXml = simplexml_load_string($definition);
        } else {
            $packageXmlStub = <<<END
<?xml version="1.0"?>
<package>
    <name />
    <version />
    <stability />
    <license />
    <channel />
    <extends />
    <summary />
    <description />
    <notes />
    <authors />
    <date />
    <time />
    <replace />
    <compatible />
    <dependencies />
</package>
END;
            $this->_packageXml = simplexml_load_string($packageXmlStub);
        }
        return $this;
    }

    /**
    * Add content to node <replace/>
    *
    * @return \Magento\Connect\Package\Hotfix
    */
    public function addReplace($path, $targetName)
    {
        $found = false;
        $parent = $this->_getNode('target', $this->_packageXml->replace, $targetName);
        $path = str_replace('\\', '/', $path);
        $directories = explode('/', dirname($path));
        foreach ($directories as $directory) {
            $parent = $this->_getNode('dir', $parent, $directory);
        }
        $fileName = basename($path);
        if ($fileName!='') {
            $fileNode = $parent->addChild('file');
            $fileNode->addAttribute('name', $fileName);
        }
        return $this;
    }

    /**
     * Add directory recursively (with subdirectory and file).
     * Exclude and Include can be add using Regular Expression.
     *
     * @param string $targetName Target name
     * @param string $targetDir Path for target name
     * @param string $path Path to directory
     * @param string $exclude Exclude
     * @param string $include Include
     * @return \Magento\Connect\Package
     */
    public function addReplaceDir($targetName, $targetDir, $path, $exclude=null, $include=null)
    {
        $targetDirLen = strlen($targetDir);
        //get all subdirectories and files.
        $entries = @glob($targetDir . $path . '/' . "*");
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $filePath = substr($entry, $targetDirLen);
                if (!empty($include) && !preg_match($include, $filePath)) {
                    continue;
                }
                if (!empty($ignore) && preg_match($exclude, $filePath)) {
                    continue;
                }
                if (is_dir($entry)) {
                    $baseName = basename($entry);
                    if ('.'===$baseName || '..'===$baseName) {
                        continue;
                    }
                    //for subdirectory call method recursively
                    $this->addReplaceDir($targetName, $targetDir, $filePath, $exclude, $include);
                } elseif (is_file($entry)) {
                    $this->addReplace($filePath, $targetName);
                }
            }
        }
        return $this;
    }

}

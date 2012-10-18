<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration file reader
 */
class Mage_Backend_Model_System_Config extends Magento_Config_XmlAbstract
{

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/config/system.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array|DOMNodeList
     */
    protected function _extractData(DOMDocument $dom)
    {
        $converter = new Mage_Backend_Model_System_Config_Converter();
        $data = $converter->convert($dom);
        return $data['config']['system'];
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><system></system></config>';
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/config/system/tab' => 'id',
            '/config/system/section' => 'id',
            '/config/system/section/group' => 'id',
            '/config/system/section/group/field' => 'id',
        );
    }

    /**
     * Retrieve all sections system configuration layout
     *
     * @return array
     */
    public function getSections()
    {
        return $this->_data['sections'];
    }

    /**
     * Retrieve list of tabs from
     *
     * @return array
     */
    public function getTabs()
    {
        return $this->_data['tabs'];
    }

    /**
     * Retrieve defined section
     *
     * @param string $sectionCode
     * @param string $websiteCode
     * @param string $storeCode
     * @return array
     */
    public function getSection($sectionCode=null, $websiteCode=null, $storeCode=null)
    {
        $key = $sectionCode ?: $websiteCode ?: $storeCode;
        return $this->_data['sections'][$key];
    }

    /**
     * Check whether node has child node that can be shown
     *
     * @param Varien_Simplexml_Element $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
    public function hasChildren($node, $websiteCode = null, $storeCode = null)
    {
        if (!$this->_canShowNode($node, $websiteCode, $storeCode)) {
            return false;
        }

        if (isset($node['groups'])) {
            $children = $node['groups'];
        } elseif (isset($node['fields'])) {
            $children = $node['fields'];
        } else {
            return true;
        }

        foreach ($children as $child) {
            if ($this->hasChildren($child, $websiteCode, $storeCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether it is possible to show the node
     *
     * @param Varien_Simplexml_Element $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
    protected function _canShowNode($node, $websiteCode = null, $storeCode = null)
    {
        $showTab = false;
        if ($storeCode) {
            $showTab = isset($node['showInStore']) ? (int)$node['showInStore'] : 0;
        } elseif ($websiteCode) {
            $showTab = isset($node['showInWebsite']) ? (int)$node['showInWebsite'] : 0;
        } elseif (isset($node['showInDefault']) && (int)$node['showInWebsite']) {
            $showTab = true;
        }

        $showTab = $showTab || $this->_app->isSingleStoreMode();
        $showTab = $showTab && !($this->_app->isSingleStoreMode()
            && isset($node['hide_in_single_store_mode']) && $node['hide_in_single_store_mode']);
        return $showTab;
    }
}

<?php
/**
 * Api Acl Config model
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_Config implements Mage_Core_Model_Acl_Config_ConfigInterface
{

    const ACL_RESOURCES_XPATH = '/config/acl/resources/*';

    const ACL_VIRTUAL_RESOURCES_XPATH = '/config/mapping/*';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Acl_Config_Reader
     */
    protected $_reader;

    /**
     * @var Mage_Webapi_Model_Authorization_Config_Reader_Factory
     */
    protected $_readerFactory;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Webapi_Model_Authorization_Config_Reader_Factory $readerFactory
     */
    public function __construct(Mage_Core_Model_Config $config,
        Mage_Webapi_Model_Authorization_Config_Reader_Factory $readerFactory
    ) {
        $this->_config = $config;
        $this->_readerFactory = $readerFactory;
    }

    /**
     * Retrieve list of acl files from each module
     *
     * @return array
     */
    protected function _getAclResourceFiles()
    {
        $files = $this->_config->getModuleConfigurationFiles('webapi' . DIRECTORY_SEPARATOR . 'acl.xml');
        return (array)$files;
    }

    /**
     * Reader object initialization
     *
     * @return Magento_Acl_Config_Reader
     */
    protected function _getReader()
    {
        if (is_null($this->_reader)) {
            $aclResourceFiles = $this->_getAclResourceFiles();
            $this->_reader = $this->_readerFactory->createReader(array($aclResourceFiles));
        }
        return $this->_reader;
    }

    /**
     * Get DOMXPath with loaded resources inside
     *
     * @return DOMXPath
     */
    protected function _getXPathResources()
    {
        $aclResources = $this->_getReader()->getAclResources();
        return new DOMXPath($aclResources);
    }

    /**
     * Return ACL Resources
     *
     * @return DOMNodeList
     */
    public function getAclResources()
    {
        return $this->_getXPathResources()->query(self::ACL_RESOURCES_XPATH);
    }

    /**
     * Return array representation of ACL resources
     *
     * @param bool $includeRoot If FALSE then only children on root element will be returned
     * @return array
     */
    public function getAclResourcesAsArray($includeRoot = true)
    {
        $result = array();
        $rootResource = null;
        $resources = $this->getAclResources();

        if ($resources && $resources->length == 1) {
            $rootResource = $resources->item(0);
        }

        if ($rootResource && $rootResource->childNodes
            && (string)$rootResource->getAttribute('id') == Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID
        ) {
            $result = $this->_parseAclResourceDOMElement($rootResource);
        }

        if (!$includeRoot) {
            $result = isset($result['children']) ? $result['children'] : array();
        }
        return $result;
    }

    /**
     * Parse DOMElement of ACL resource in config and return it's array representation
     *
     * @param DOMElement $node
     * @return array
     */
    protected function _parseAclResourceDOMElement(DOMElement $node)
    {
        $result = array();

        $result['id'] = (string)$node->getAttribute('id');
        $result['text'] = (string)$node->getAttribute('title');
        $sortOrder = (string)$node->getAttribute('sortOrder');
        if (!empty($sortOrder)) {
            $result['sortOrder']= $sortOrder;
        }
        //$result['sortOrder']= !empty($sortOrder) ? (int)$sortOrder : null;

        if (empty($node->childNodes)) {
            return $result;
        }

        $result['children'] = array();
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $result['children'][] = $this->_parseAclResourceDOMElement($child);
            }
        }

        if (!empty($result['children'])) {
            $this->_sortBySortOrder($result['children']);
        }

        return $result;
    }

    /**
     * @param $data
     */
    protected function _sortBySortOrder(&$data)
    {
        $sortCallback = function ($firstItem, $secondItem) {
            if (!isset($firstItem['sortOrder']) && isset($secondItem['sortOrder'])) {
                return 1;
            }

            if (isset($firstItem['sortOrder']) && !isset($secondItem['sortOrder'])) {
                return -1;
            }

            if ((!isset($secondItem['sortOrder']) && !isset($firstItem['sortOrder']))
                || ($firstItem['sortOrder'] == $secondItem['sortOrder'])
            ) {
                return 0;
            } elseif ($firstItem['sortOrder'] < $secondItem['sortOrder']) {
                return -1;
            } else {
                return 1;
            }

        };
        usort($data, $sortCallback);
    }

    /**
     * Return ACL Virtual Resources
     *
     * Virtual resources are not shown in resource list, they use existing resource to check permission
     *
     * @return DOMNodeList
     */
    public function getAclVirtualResources()
    {
        return $this->_getXPathResources()->query(self::ACL_VIRTUAL_RESOURCES_XPATH);
    }
}

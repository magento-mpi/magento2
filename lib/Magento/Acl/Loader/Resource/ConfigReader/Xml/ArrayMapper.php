<?php
/**
 * Converted XML to ACL builder array format mapper.
 * Translates array retrieved from xml array converter to format consumed by acl builder
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper
{
    /**
     * Map configuration
     *
     * @param array $xmlAsArray
     * @return array
     */
    public function map(array $xmlAsArray)
    {
        $result = array();
        foreach ($xmlAsArray as $item) {
            $resultItem = $item['__attributes__'];
            if (isset($resultItem['disabled']) && ($resultItem['disabled'] == 1 || $resultItem['disabled'] == 'true')) {
                continue;
            }
            unset($resultItem['disabled']);
            $resultItem['sortOrder'] = isset($resultItem['sortOrder']) ? $resultItem['sortOrder'] : 0;
            if (isset($item['resource'])) {
                $resultItem['children'] = $this->map($item['resource']);
            }
            $result[] = $resultItem;
        }
        usort($result, array($this, '_sortTree'));
        return $result;
    }

    /**
     * Sort ACL resource nodes
     *
     * @param array $nodeA
     * @param array $nodeB
     * @return int
     */
    protected function _sortTree(array $nodeA, array $nodeB)
    {
        return $nodeA['sortOrder'] < $nodeB['sortOrder'] ? -1 : ($nodeA['sortOrder'] > $nodeB['sortOrder'] ? 1 : 0);
    }
}

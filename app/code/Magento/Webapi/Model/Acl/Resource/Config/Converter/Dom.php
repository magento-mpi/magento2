<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_Config_Converter_Dom extends Magento_Acl_Resource_Config_Converter_Dom
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $aclResourceConfig = parent::convert($source);
        $aclResourceConfig['config']['mapping'] = array();
        $xpath = new DOMXPath($source);
        /** @var $mappingNode DOMNode */
        foreach ($xpath->query('/config/mapping/resource') as $mappingNode) {
            $mappingData = array();
            $mappingAttributes = $mappingNode->attributes;
            $idNode = $mappingAttributes->getNamedItem('id');
            if (is_null($idNode)) {
                throw new Exception('Attribute "id" is required for ACL resource mapping.');
            }
            $mappingData['id'] = $idNode->nodeValue;

            $parentNode = $mappingAttributes->getNamedItem('parent');
            if (is_null($parentNode)) {
                throw new Exception('Attribute "parent" is required for ACL resource mapping.');
            }
            $mappingData['parent'] = $parentNode->nodeValue;
            $aclResourceConfig['config']['mapping'][] = $mappingData;
        }
        return $aclResourceConfig;
    }
}


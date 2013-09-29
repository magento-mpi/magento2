<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Acl\Resource\Config\Converter;

class Dom implements \Magento\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $aclResourceConfig = array('config' => array('acl' => array('resources' => array())));
        $xpath = new \DOMXPath($source);
        /** @var $resourceNode \DOMNode */
        foreach ($xpath->query('/config/acl/resources/resource') as $resourceNode) {
            $aclResourceConfig['config']['acl']['resources'][] = $this->_convertResourceNode($resourceNode);
        }
        return $aclResourceConfig;
    }

    /**
     * Convert resource node into assoc array
     *
     * @param \DOMNode $resourceNode
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _convertResourceNode(\DOMNode $resourceNode)
    {
        $resourceData = array();
        $resourceAttributes = $resourceNode->attributes;
        $idNode = $resourceAttributes->getNamedItem('id');
        if (is_null($idNode)) {
            throw new \Exception('Attribute "id" is required for ACL resource.');
        }
        $resourceData['id'] = $idNode->nodeValue;
        $moduleNode = $resourceAttributes->getNamedItem('module');
        if (!is_null($moduleNode)) {
            $resourceData['module'] = $moduleNode->nodeValue;
        }
        $titleNode = $resourceAttributes->getNamedItem('title');
        if (!is_null($titleNode)) {
            $resourceData['title'] = $titleNode->nodeValue;
        }
        $sortOrderNode = $resourceAttributes->getNamedItem('sortOrder');
        $resourceData['sortOrder'] = (!is_null($sortOrderNode)) ? (int)$sortOrderNode->nodeValue : 0;
        $disabledNode = $resourceAttributes->getNamedItem('disabled');
        $resourceData['disabled'] =  (!is_null($disabledNode) && $disabledNode->nodeValue == 'true') ? true : false;
        // convert child resource nodes if needed
        $resourceData['children'] = array();
        /** @var $childNode \DOMNode */
        foreach ($resourceNode->childNodes as $childNode) {
            if ($childNode->nodeName == 'resource') {
                $resourceData['children'][] = $this->_convertResourceNode($childNode);
            }
        }
        return $resourceData;
    }
}


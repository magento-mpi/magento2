<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webapi XML Renderer Writer.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Rest_Renderer_Xml_Writer extends Zend_Config_Writer_Xml
{
    /**
     * Root node in XML output
     */
    const XML_ROOT_NODE = 'magento_api';

    /**
     * Render a Zend_Config into a XML config string.
     * OVERRIDE to avoid using zend-config string in XML.
     *
     * @return string
     */
    public function render()
    {
        // TODO: Consider if SimpleXML should be replaced with DOMDocument
        $xml = new SimpleXMLElement('<' . self::XML_ROOT_NODE . '/>');
        $extends = $this->_config->getExtends();
        $sectionName = $this->_config->getSectionName();

        if (is_string($sectionName)) {
            $child = $xml->addChild($sectionName);

            $this->_addBranch($this->_config, $child, $xml);
        } else {
            foreach ($this->_config as $sectionName => $data) {
                if (!($data instanceof Zend_Config)) {
                    $xml->addChild($sectionName, (string)$data);
                } else {
                    $child = $xml->addChild($sectionName);

                    if (isset($extends[$sectionName])) {
                        $child->addAttribute('zf:extends', $extends[$sectionName], Zend_Config_Xml::XML_NAMESPACE);
                    }

                    $this->_addBranch($data, $child, $xml);
                }
            }
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}

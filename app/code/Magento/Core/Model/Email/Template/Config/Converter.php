<?php
/**
 * Converter of email templates configuration from DOMDocument to array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Email_Template_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $result = array();
        /** @var DOMNode $templateNode */
        foreach ($source->documentElement->childNodes as $templateNode) {
            if ($templateNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $templateId = $templateNode->attributes->getNamedItem('id')->nodeValue;
            $templateLabel = $templateNode->attributes->getNamedItem('label')->nodeValue;
            $templateFile = $templateNode->attributes->getNamedItem('file')->nodeValue;
            $templateType = $templateNode->attributes->getNamedItem('type')->nodeValue;
            $templateModule = $templateNode->attributes->getNamedItem('module')->nodeValue;
            $result[$templateId] = array(
                'label' => $templateLabel,
                'file' => $templateFile,
                'type' => $templateType,
                'module' => $templateModule,
            );
        }
        return $result;
    }
}

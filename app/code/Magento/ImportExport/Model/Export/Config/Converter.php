<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ImportExport_Model_Export_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    public function convert($source)
    {
        $output = array(
            'entities' => array(),
            'productTypes' => array(),
            'fileFormats' => array(),
        );
        /** @var DOMNodeList $entities */
        $entities = $source->getElementsByTagName('entity');
        /** @var DOMNode $entityConfig */
        foreach ($entities as $entityConfig) {
            $attributes = $entityConfig->attributes;
            $name = $attributes->getNamedItem('name')->nodeValue;
            $label = $attributes->getNamedItem('label')->nodeValue;
            $model = $attributes->getNamedItem('model')->nodeValue;

            $output['entities'][$name] = array(
                'name' => $name,
                'label' => $label,
                'model' => $model,
            );
        }

        /** @var DOMNodeList $productTypes */
        $productTypes = $source->getElementsByTagName('productType');
        /** @var DOMNode $productTypeConfig */
        foreach ($productTypes as $productTypeConfig) {
            $attributes = $productTypeConfig->attributes;
            $model = $attributes->getNamedItem('model')->nodeValue;
            $name = $attributes->getNamedItem('name')->nodeValue;

            $output['productTypes'][$name] = array(
                'name' => $name,
                'model' => $model,
            );
        }

        /** @var DOMNodeList $fileFormats */
        $fileFormats = $source->getElementsByTagName('fileFormat');
        /** @var DOMNode $fileFormatConfig */
        foreach ($fileFormats as $fileFormatConfig) {
            $attributes = $fileFormatConfig->attributes;
            $name = $attributes->getNamedItem('name')->nodeValue;
            $model = $attributes->getNamedItem('model')->nodeValue;
            $label = $attributes->getNamedItem('label')->nodeValue;

            $output['fileFormats'][$name] = array(
                'name' => $name,
                'model' => $model,
                'label' => $label,
            );
        }
        return $output;
    }
}

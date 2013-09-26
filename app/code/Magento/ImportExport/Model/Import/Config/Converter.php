<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ImportExport_Model_Import_Config_Converter implements Magento_Config_ConverterInterface
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
        );
        /** @var DOMNodeList $events */
        $entities = $source->getElementsByTagName('entity');
        /** @var DOMNode $entityConfig */
        foreach ($entities as $entityConfig) {
            $attributes = $entityConfig->attributes;
            $name = $attributes->getNamedItem('name')->nodeValue;
            $label = $attributes->getNamedItem('label')->nodeValue;
            $behaviorModel = $attributes->getNamedItem('behaviorModel')->nodeValue;
            $model = $attributes->getNamedItem('model')->nodeValue;

            $output['entities'][$name] = array(
                'name' => $name,
                'label' => $label,
                'behaviorModel' => $behaviorModel,
                'model' => $model,
            );
        }

        /** @var DOMNodeList $events */
        $productTypes = $source->getElementsByTagName('productType');
        /** @var DOMNode $productTypeConfig */
        foreach ($productTypes as $productTypeConfig) {
            $attributes = $productTypeConfig->attributes;
            $name = $attributes->getNamedItem('name')->nodeValue;
            $model = $attributes->getNamedItem('model')->nodeValue;

            $output['productTypes'][$name] = array(
                'name' => $name,
                'model' => $model,
            );
        }
        return $output;
    }
}

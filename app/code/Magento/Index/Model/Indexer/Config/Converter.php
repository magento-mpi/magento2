<?php
/**
 * Indexers configuration converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert config
     *
     * @param mixed $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();

        /** @var DOMNodeList $indexers */
        $indexers = $source->getElementsByTagName('indexer');

        /** @var DOMNode $indexer */
        foreach ($indexers as $indexer) {
            $indexerConfig = array();
            foreach ($indexer->attributes as $attribute) {
                $indexerConfig[$attribute->nodeName] = $attribute->nodeValue;
            }

            $dependencies = array();
            /** @var DOMNode $dependency */
            foreach ($indexer->getElementsByTagName('depends') as $dependency) {
                $dependencies[] = $dependency->attributes->getNamedItem('name')->nodeValue;
            }
            $indexerConfig['depends'] = $dependencies;
            $output[$indexer->attributes->getNamedItem('name')->nodeValue] = $indexerConfig;
        }

        return $output;
    }
}
<?php
/**
 * Converter of event observers configuration from \DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Event\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = array();
        /** @var \DOMNodeList $events */
        $events = $source->getElementsByTagName('event');
        /** @var \DOMNode $eventConfig */
        foreach ($events as $eventConfig) {
            $eventName = $eventConfig->attributes->getNamedItem('name')->nodeValue;
            $eventObservers = array();
            /** @var \DOMNode $observerConfig */
            foreach ($eventConfig->childNodes as $observerConfig) {
                if ($observerConfig->nodeName != 'observer' || $observerConfig->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $observerNameNode = $observerConfig->attributes->getNamedItem('name');
                if (!$observerNameNode) {
                    throw new \InvalidArgumentException('Attribute name is missed');
                }
                $config = $this->_convertObserverConfig($observerConfig);
                $config['name'] = $observerNameNode->nodeValue;
                $eventObservers[$observerNameNode->nodeValue] = $config;
            }
            $output[$eventName] = $eventObservers;
        }
        return $output;
    }

    /**
     * Convert observer configuration
     *
     * @param \DOMNode $observerConfig
     * @return array
     */
    public function _convertObserverConfig($observerConfig)
    {
        $output = array();
        /** Parse instance configuration */
        $instanceAttribute = $observerConfig->attributes->getNamedItem('instance');
        if ($instanceAttribute) {
            $output['instance'] = $instanceAttribute->nodeValue;
        }

        /** Parse instance method configuration */
        $methodAttribute = $observerConfig->attributes->getNamedItem('method');
        if ($methodAttribute) {
            $output['method'] = $methodAttribute->nodeValue;
        }

        /** Parse disabled/enabled configuration */
        $disabledAttribute = $observerConfig->attributes->getNamedItem('disabled');
        if ($disabledAttribute && $disabledAttribute->nodeValue == 'true') {
            $output['disabled'] = true;
        }

        /** Parse shareability configuration */
        $shredAttribute = $observerConfig->attributes->getNamedItem('shared');
        if ($shredAttribute && $shredAttribute->nodeValue == 'false') {
            $output['shared'] = false;
        }

        return $output;
    }
}

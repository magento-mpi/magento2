<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Container as ContainerInterface;
use Magento\View\Layout\File\Source\Aggregated;
use Magento\Core\Model\Layout\Argument\Processor;
use Magento\Core\Model\Layout\Element as LayoutElement;

class Reader
{
    /**
     * @var array
     */
    protected $meta;

    /**
     * @var Aggregated
     */
    protected $fileSource;

    /**
     * @var Processor
     */
    protected $argumentProcessor;

    /**
     * @var array
     */
    protected $xmlElements = array();

    /**
     * @var int
     */
    protected $cnt = 0;

    /**
     * @param Aggregated $fileSource
     * @param Processor $argumentProcessor
     */
    public function __construct(
        Aggregated $fileSource,
        Processor $argumentProcessor
    ) {
        $this->fileSource = $fileSource;
        $this->argumentProcessor = $argumentProcessor;
    }

    /**
     * @param LayoutElement $xml
     * @param array $meta
     */
    public function generateFromXml($xml, &$meta)
    {
        $this->meta = &$meta;
        $this->xmlAsArray($xml, $meta);
    }

    /**
     * @param $handle
     * @param ContainerInterface $container
     */
    public function loadHandle($handle, ContainerInterface $container)
    {
        // TODO: find a better way of assembling elements declarations
        $this->meta = & $container->getMeta();

        // TODO: try cache first

        $updateFiles = $this->fileSource->getFiles($container->getDesignTheme(), $handle);
        foreach ($updateFiles as $file) {
            /** @var $file \Magento\Core\Model\Layout\File */
            $fileStr = file_get_contents($file->getFilename());
            /** @var $fileXml \Magento\Core\Model\Layout\Element */
            $fileXml = $this->loadXmlString($fileStr);
            $this->xmlAsArray($fileXml, $this->meta);
        }
    }

    /**
     * @param $xmlString
     * @return \SimpleXMLElement
     */
    protected function loadXmlString($xmlString)
    {
        return simplexml_load_string($xmlString, 'Magento\\View\\Layout\\Element');
    }

    /**
     * @param array $raw
     * @return array
     */
    protected function parseRawHandle(array $raw)
    {
        $result = array();
        foreach ($raw as $key => $value) {
            if (!is_array($value)) {
                $result[$key] = $value;
            } else {
                $_val = isset($value['@']) ? $value['@'] : array();
                if ('block' === $key) {
                    $_val['type'] = 'container';
                    $result[$_val['name']] = $_val;
                } elseif ('action' === $key) {
                    $_val['type'] = 'container';
                    $result[$_val['name']] = $_val;
                }
            }
        }
        return $result;
    }

    /**
     * @param LayoutElement $xml
     * @param array $container
     * @param string $parentName
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function xmlAsArray(LayoutElement $xml, &$container, $parentName = null)
    {
        foreach ($xml->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $container[$attributeName] = (string)$attribute;
            }
        }

        if (null === $parentName) {
            $container['type'] = 'container';
            $container['name'] = '.';
        }

        if (empty($container['name'])) {
            if ($xml->getAttribute('name')) {
                $elementName = $xml->getAttribute('name');
            } else {
                $elementName = 'arguments-' . $this->cnt++;
            }
        } else {
            $elementName = $container['name'];
        }

        // add children values
        if ($xml->hasChildren()) {
            foreach ($xml->children() as $name => $childXml) {
                if ($childXml->getAttribute('name')) {
                    $childName = $childXml->getAttribute('name');
                } else {
                    $childName = 'arguments-' . $this->cnt++;
                }

                if (in_array($name, array('reference', 'referenceBlock', 'referenceContainer'))) {
                    $targetContainer = & $this->seek($this->meta, $childName);
                    $this->xmlAsArray($childXml, $targetContainer, $name);
                } elseif ($name === 'arguments') {
                    $arguments = $this->parseArguments($childXml);
                    $container['arguments'] = $this->processArguments($arguments);
                } else {
                    $container['children'][$childName]['type'] = $name;
                    $container['children'][$childName]['name'] = $childName;
                    $container['children'][$childName]['parent'] = $elementName;
                    $this->xmlAsArray($childXml, $container['children'][$childName], $name);
                }
            }
        } else {
            if (empty($container)) {
                // return as string, if nothing was found
                $container = (string) $this;
            } else {
                // value has zero key element
                $container['value'] = (string) $xml;
            }
        }
    }

    /**
     * @param $original
     * @param $elementName
     * @return null
     */
    protected function & seek(& $original, $elementName)
    {
        if (isset($original['children'][$elementName])) {
            return $original['children'][$elementName];
        }

        if (isset($original['children'])) {
            foreach (array_keys($original['children']) as $key) {
                $result = & $this->seek($original['children'][$key], $elementName);
                if ($result) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * Parse argument nodes and create prepared array of items
     *
     * @param LayoutElement $node
     * @return array
     */
    protected function parseArguments(LayoutElement $node)
    {
        $arguments = array();
        foreach ($node->xpath('argument') as $argument) {
            /** @var $argument LayoutElement */
            $argumentName = (string)$argument['name'];
            $arguments[$argumentName] = $this->argumentProcessor->parse($argument);
        }
        return $arguments;
    }

    /**
     * Process arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function processArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $name => $argument) {
            $result[$name] = $this->argumentProcessor->process($argument);
        }
        return $result;
    }
}

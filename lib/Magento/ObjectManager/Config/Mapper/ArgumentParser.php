<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Mapper;

use Magento\Config\Converter\Dom\Flat as FlatConverter;
use Magento\Config\Dom\NodePathConfig;

/**
 * Parser of a DI argument node that returns its array representation with no data loss
 */
class ArgumentParser
{
    /**
     * @var FlatConverter
     */
    private $converter;

    /**
     * Build and return array representation of layout argument node
     *
     * @param \DOMNode $argumentNode
     * @return array|string
     */
    public function parse(\DOMNode $argumentNode)
    {
        // Base path is specified to use more meaningful XPaths in config
        return $this->getConverter()->convert($argumentNode, 'argument');
    }

    /**
     * Retrieve instance of XML converter, suitable for DI argument nodes
     *
     * @return FlatConverter
     */
    protected function getConverter()
    {
        if (!$this->converter) {
            $this->converter = new FlatConverter(
                new NodePathConfig(array(
                    'argument(/item)+' => 'name',
                ))
            );
        }
        return $this->converter;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Argument;

use Magento\Framework\Config\Converter\Dom\Flat as FlatConverter;
use Magento\Framework\Config\Dom\NodePathMatcher;
use Magento\Framework\Config\Dom\ArrayNodeConfig;

/**
 * Parser of a layout argument node that returns its array representation with no data loss
 */
class Parser
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
     * Retrieve instance of XML converter, suitable for layout argument nodes
     *
     * @return FlatConverter
     */
    protected function getConverter()
    {
        if (!$this->converter) {
            $arrayNodeConfig = new ArrayNodeConfig(
                new NodePathMatcher(),
                array('argument/param' => 'name', 'argument(/item)+' => 'name', 'argument(/item)+/param' => 'name'),
                array('argument/updater')
            );
            $this->converter = new FlatConverter($arrayNodeConfig);
        }
        return $this->converter;
    }
}

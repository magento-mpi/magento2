<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content\Dom;

/**
 * Class NodeMergingConfig
 * Configuration of identifier attributes to be taken into account during merging
 * @package Magento\Doc\Document\Content\Dom
 */
class NodeMergingConfig
{
    /**
     * @var NodePathMatcher
     */
    private $nodePathMatcher;

    /**
     * Format: array('node' => '<node_id_attribute>', ...)
     *
     * @var array
     */
    private $idAttributes = [];

    /**
     * @param NodePathMatcher $nodePathMatcher
     * @param array $idAttributes
     */
    public function __construct(NodePathMatcher $nodePathMatcher, array $idAttributes)
    {
        $this->nodePathMatcher = $nodePathMatcher;
        $this->idAttributes = $idAttributes;
    }

    /**
     * Retrieve name of an identifier attribute for a node
     *
     * @param string $nodeXpath
     * @return string|null
     */
    public function getIdAttribute($nodeXpath)
    {
        foreach ($this->idAttributes as $pathPattern => $idAttribute) {
            if ($pathPattern === '*') {
                continue;
            }
            if ($this->nodePathMatcher->match($pathPattern, $nodeXpath)) {
                return $idAttribute;
            }
        }
        return isset($this->idAttributes['*']) ? $this->idAttributes['*'] : null;
    }
}

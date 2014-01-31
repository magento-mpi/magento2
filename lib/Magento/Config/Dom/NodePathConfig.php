<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config\Dom;

/**
 * Association of arbitrary information with XML nodes
 */
class NodePathConfig
{
    /**
     * @var array Format: array('/some/static/path' => <node_info>, '/some/regexp/path(/item)+' => <node_info>, ...)
     */
    private $pathInfoMap;

    /**
     * @param array $pathInfoMap
     */
    public function __construct(array $pathInfoMap)
    {
        $this->pathInfoMap = $pathInfoMap;
    }

    /**
     * Retrieve information associated with a node identified by XPath
     *
     * @param string $xpath
     * @return mixed
     */
    public function getNodeInfo($xpath)
    {
        $path = preg_replace('/\[@[^\]]+?\]/', '', $xpath);
        $path = preg_replace('/\/[^:]+?\:/', '/', $path);
        foreach ($this->pathInfoMap as $pathPattern => $result) {
            $pathPattern = '#^' . $pathPattern . '$#';
            if (preg_match($pathPattern, $path)) {
                return $result;
            }
        }
        return null;
    }
}

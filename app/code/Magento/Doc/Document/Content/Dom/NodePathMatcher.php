<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content\Dom;

/**
 * Class NodePathMatcher
 * Matching of XPath expressions to path patterns
 * @package Magento\Doc\Document\Content\Dom
 */
class NodePathMatcher
{
    /**
     * Whether a subject XPath matches to a given path pattern
     *
     * @param string $pathPattern Example: '/some/static/path' or '/some/regexp/path(/item)+'
     * @param string $xpathSubject Example: '/some[@attr="value"]/static/ns:path'
     * @return bool
     */
    public function match($pathPattern, $xpathSubject)
    {
        $pathSubject = $this->simplifyXpath($xpathSubject);
        $pathPattern = '#^' . $pathPattern . '$#';
        return (bool)preg_match($pathPattern, $pathSubject);
    }

    /**
     * Strip off predicates and namespaces from the XPath
     *
     * @param string $xpath
     * @return string
     */
    protected function simplifyXpath($xpath)
    {
        $result = $xpath;
        $result = preg_replace('/\[@[^\]]+?\]/', '', $result);
        $result = preg_replace('/\/[^:]+?\:/', '/', $result);
        return $result;
    }
}

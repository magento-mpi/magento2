<?php
/**
 * Wrapper around \Magento\Core\Controller\Request\Http for the Navigator class.
 *
 * HTTP Requests need to be exposed as data services for the front end (twig) to be able to access the
 * request data. This class acts as a wrapper around the \Magento\Core\Controller\Request\Http object so
 * that the data can be searched for and extracted via the Navigator class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Path;

class Request implements \Magento\Core\Model\DataService\Path\NodeInterface
{
    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function __construct(\Magento\Core\Controller\Request\Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return \Magento\Core\Model\DataService\Path\NodeInterface|mixed|null the child node,
     *    or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        switch ($pathElement) {
            case 'params':
                return $this->_request->getParams();
        }

        return null;
    }
}

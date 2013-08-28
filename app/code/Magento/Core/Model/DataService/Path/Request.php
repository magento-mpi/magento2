<?php
/**
 * Wrapper around Magento_Core_Controller_Request_Http for the Navigator class.
 *
 * HTTP Requests need to be exposed as data services for the front end (twig) to be able to access the
 * request data. This class acts as a wrapper around the Magento_Core_Controller_Request_Http object so
 * that the data can be searched for and extracted via the Navigator class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Path_Request implements Magento_Core_Model_DataService_Path_NodeInterface
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(Magento_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Magento_Core_Model_DataService_Path_NodeInterface|mixed|null the child node,
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

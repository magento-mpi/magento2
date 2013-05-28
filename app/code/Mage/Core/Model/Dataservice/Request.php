<?php
/**
 * Wrapper around Mage_Core_Controller_Request_Http for the Navigator class.
 *
 * HTTP Requests need to be exposed as data services for the front end (twig) to be able to access the
 * request data. This class acts as a wrapper around the Mage_Core_Controller_Request_Http object so
 * that the data can be searched for and extracted via the Navigator class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Request implements Mage_Core_Model_Dataservice_Path_Node
{
    protected $_request;

    public function __construct(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_Node|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChild($pathElement)
    {
        switch ($pathElement) {
            case 'params':
                return $this->_request->getParams();
        }

        return null;
    }
}
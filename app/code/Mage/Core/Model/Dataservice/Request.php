<?php
/**
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
     * Returns a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_Node the child node
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
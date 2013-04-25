<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Datasource_Request_Visitor implements Mage_Core_Model_Datasource_Path_Visitable
{
    protected $_request;

    public function __construct(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    public function visit(Mage_Core_Model_Datasource_Path_Visitor $visitor)
    {
        $target = $visitor->getCurrentPathElement();
        switch ($target) {
            case 'params':
                return $this->_request->getParams();
        }
    }
}
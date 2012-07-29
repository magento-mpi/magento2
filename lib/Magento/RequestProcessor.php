<?php
/**
* {license_notice}
*
* @category    Magento
* @package     Magento_Profiler
* @copyright   {copyright}
* @license     {license_link}
*/

interface Magento_RequestProcessor
{
    /**
     * @abstract
     * @param Zend_Controller_Request_Abstract $request
     * @return mixed
     */
    public function process(Zend_Controller_Request_Abstract $request);
}

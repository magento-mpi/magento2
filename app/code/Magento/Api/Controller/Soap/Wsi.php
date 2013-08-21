<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SOAP WS-I compatible API controller.
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Controller_Soap_Wsi extends Magento_Api_Controller_Action
{
    public function indexAction()
    {
        $handlerName = 'soap_wsi';
        /* @var $server Magento_Api_Model_Server */
        $this->_getServer()->init($this, $handlerName, $handlerName)->run();
    }
}

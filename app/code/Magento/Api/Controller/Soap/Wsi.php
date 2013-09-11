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
namespace Magento\Api\Controller\Soap;

class Wsi extends \Magento\Api\Controller\Action
{
    public function indexAction()
    {
        $handlerName = 'soap_wsi';
        /* @var $server \Magento\Api\Model\Server */
        $this->_getServer()->init($this, $handlerName, $handlerName)->run();
    }
}

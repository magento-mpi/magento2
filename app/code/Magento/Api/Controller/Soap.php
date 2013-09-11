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
 * SOAP API controller.
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Controller;

class Soap extends \Magento\Api\Controller\Action
{
    public function indexAction()
    {
        $handlerName = 'soap_v2';
        /* @var $server \Magento\Api\Model\Server */
        $this->_getServer()->init($this, $handlerName, $handlerName)->run();
    }
}

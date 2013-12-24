<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Stub;

use Magento\Backend\App\Action;
use Magento\Sales\Controller\Adminhtml\Order as OrderController;

/**
 * Magento Adminhtml Order Controller Test
 */
class Order extends OrderController
{
    /**
     * @var \Magento\App\Action\Title
     */
    public $_title;

    /**
     * @var \Magento\App\Action\Title
     */
    public $_view;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    public $messageManager;
}

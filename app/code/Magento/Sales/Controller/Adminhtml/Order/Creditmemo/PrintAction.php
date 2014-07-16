<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Creditmemo;

use \Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action;

class PrintAction extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo\PrintAction
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        parent::__construct($context, $fileFactory);
    }

    /**
     * Create pdf for current creditmemo
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $this->_title->add(__('Credit Memos'));
        $this->creditmemoLoader->load($this->_request);
        parent::execute();
    }
}

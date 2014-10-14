<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Index;

class ChangeLocale extends \Magento\Backend\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\RedirectFactory $redirectFactory
    ) {
        $this->redirectFactory = $redirectFactory;
        parent::__construct($context);
    }

    /**
     * Change locale action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $redirectResult = $this->redirectFactory->create();
        $redirectResult->setRefererUrl();
        return $redirectResult;
    }
}

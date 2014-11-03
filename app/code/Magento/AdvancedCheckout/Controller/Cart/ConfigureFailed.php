<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\Controller\Result;
use Magento\Framework\View\Result\PageFactory;

class ConfigureFailed extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var Result\Redirect
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param ActionContext $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        ActionContext $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        Result\RedirectFactory $resultRedirectFactory,
        PageFactory $resultPageFactory
    ) {
        $this->viewHelper = $viewHelper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Configure failed item options
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty', 1);

        try {
            $params = new \Magento\Framework\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $buyRequest = new \Magento\Framework\Object(array('product' => $id, 'qty' => $qty));
            $params->setBuyRequest($buyRequest);
            $params->setBeforeHandles(array('catalog_product_view'));
            $page = $this->resultPageFactory->create();
            $this->viewHelper->prepareAndRender($page, $id, $this, $params);
            return $page;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*');
        } catch (\Exception $e) {
            $this->messageManager->addError(__('You cannot configure a product.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*');
        }
    }
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

class IgnoreTaxNotification extends \Magento\Tax\Controller\Adminhtml\Tax
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $taxClassService, $taxClassBuilder);
    }

    /**
     * Set tax ignore notification flag and redirect back
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $section = $this->getRequest()->getParam('section');
        if ($section) {
            try {
                $path = 'tax/notification/ignore_' . $section;
                $this->_objectManager->get('\Magento\Core\Model\Resource\Config')
                    ->saveConfig($path, 1, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, 0);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        // clear the block html cache
        $this->_cacheTypeList->cleanType('block_html');
        $this->_eventManager->dispatch('adminhtml_cache_refresh_type', array('type' => 'block_html'));

        $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}

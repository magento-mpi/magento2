<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Gift registry frontend search controller
 */
class Search extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_localeDate = $localeDate;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context);
    }

    /**
     * Check if gift registry is enabled on current store before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * Initialize gift registry type model
     *
     * @param int $typeId
     * @return \Magento\GiftRegistry\Model\Type
     */
    protected function _initType($typeId)
    {
        $type = $this->_objectManager->create(
            'Magento\GiftRegistry\Model\Type'
        )->setStoreId(
            $this->_storeManager->getStore()->getId()
        )->load(
            $typeId
        );

        $this->_coreRegistry->register('current_giftregistry_type', $type);
        return $type;
    }
}

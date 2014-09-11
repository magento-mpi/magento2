<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Model;

use Magento\Checkout\Model\Agreements\AgreementsProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provide Agreements stored in db
 */
class AgreementsProvider implements AgreementsProviderInterface
{
    /** Path to config node */
    const PATH_ENABLED = 'checkout/options/enable_agreements';

    /** @var \Magento\CheckoutAgreements\Model\Resource\Agreement\CollectionFactory */
    protected $agreementCollectionFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var  \Magento\Framework\StoreManagerInterface */
    protected $storeManager;

    /**
     * @param Resource\Agreement\CollectionFactory $agreementCollectionFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\CheckoutAgreements\Model\Resource\Agreement\CollectionFactory $agreementCollectionFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->agreementCollectionFactory = $agreementCollectionFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get list of required Agreement Ids
     *
     * @return int[]
     */
    public function getRequiredAgreementIds()
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return [];
        } else {
            return $this->agreementCollectionFactory->create()
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->addFieldToFilter('is_active', 1)
                ->getAllIds();
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Block\Adminhtml\Rate;

use Magento\Tax\Controller\RegistryConstants;

/**
 * Tax Rate Titles Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Title extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $_titles;

    /**
     * @var string
     */
    protected $_template = 'rate/title.phtml';

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    protected $_taxRateService;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_taxRateService = $taxRateService;
        $this->_storeFactory = $storeFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = array();

            $taxRateId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_TAX_RATE_ID);
            $titles = array();
            if ($taxRateId) {
                $rate = $this->_taxRateService->getTaxRate($taxRateId);
                $titles = $rate->getTitles();
            }

            foreach ($titles as $title) {
                $this->_titles[$title->getStoreId()] = $title->getValue();
            }
            foreach ($this->getStores() as $store) {
                if (!isset($this->_titles[$store->getId()])) {
                    $this->_titles[$store->getId()] = '';
                }
            }
        }
        return $this->_titles;
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = $this->_storeFactory->create()->getResourceCollection()->setLoadDefault(false)->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }
}

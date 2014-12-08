<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance;

use Magento\Customer\Controller\RegistryConstants;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getCustomerWebsite()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER)->getWebsiteId();
    }

    /**
     * @return string
     */
    public function getWebsitesJson()
    {
        $result = [];
        foreach ($this->_storeManager->getWebsites() as $websiteId => $website) {
            $result[$websiteId] = [
                'name' => $website->getName(),
                'website_id' => $websiteId,
                'currency_code' => $website->getBaseCurrencyCode(),
                'groups' => [],
            ];

            foreach ($website->getGroups() as $groupId => $group) {
                $result[$websiteId]['groups'][$groupId] = ['name' => $group->getName()];

                foreach ($group->getStores() as $storeId => $store) {
                    $result[$websiteId]['groups'][$groupId]['stores'][] = [
                        'name' => $store->getName(),
                        'store_id' => $storeId,
                    ];
                }
            }
        }

        return $this->_jsonEncoder->encode($result);
    }
}

<?php
/**
 * Checkout Resource Setup Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Customer\Helper\Address $customerAddress,
        $moduleName = 'Magento_Checkout',
        $connectionName = ''
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct(
            $context, $resourceName, $cache, $attrGroupCollectionFactory, $moduleName, $connectionName
        );
    }

    /**
     * @return \Magento\Customer\Helper\Address
     */
    public function getCustomerAddress()
    {
        return $this->_customerAddress;
    }
}

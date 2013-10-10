<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Checkout Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Customer\Helper\Address $customerAddress,
        $resourceName,
        $moduleName = 'Magento_Checkout',
        $connectionName = ''
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct($context, $cache, $attrGrCollFactory, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\Customer\Helper\Address
     */
    public function getCustomerAddress()
    {
        return $this->_customerAddress;
    }
}

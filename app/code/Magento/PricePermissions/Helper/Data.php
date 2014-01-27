<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price Permissions Data Helper
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PricePermissions\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Path to edit_product_price node in ACL
     *
     * Used to check if admin has permission to edit product price
     */
    const EDIT_PRODUCT_PRICE_ACL_PATH = 'Magento_PricePermissions::edit_product_price';

    /**
     * Path to read_product_price node in ACL
     *
     * Used to check if admin has permission to read product price
     */
    const READ_PRODUCT_PRICE_ACL_PATH = 'Magento_PricePermissions::read_product_price';

    /**
     * Path to edit_product_status node in ACL
     *
     * Used to check if admin has permission to edit product status
     */
    const EDIT_PRODUCT_STATUS_ACL_PATH = 'Magento_PricePermissions::edit_product_status';

    /**
     * Path to default_product_price node in config
     */
    const DEFAULT_PRODUCT_PRICE_CONFIG_PATH = 'catalog/price/default_product_price';

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\App\ConfigInterface $coreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\AuthorizationInterface $authorization,
        \Magento\App\ConfigInterface $coreConfig
    ) {
        parent::__construct($context);
        $this->_authorization = $authorization;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Check if admin has permissions to read product price
     *
     * @return boolean
     */
    public function getCanAdminReadProductPrice()
    {
        return (boolean) $this->_authorization->isAllowed(self::READ_PRODUCT_PRICE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit product price
     *
     * @return boolean
     */
    public function getCanAdminEditProductPrice()
    {
        return (boolean) $this->_authorization->isAllowed(self::EDIT_PRODUCT_PRICE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit product status
     *
     * @return boolean
     */
    public function getCanAdminEditProductStatus()
    {
        return (boolean) $this->_authorization->isAllowed(self::EDIT_PRODUCT_STATUS_ACL_PATH);
    }

    /**
     * Retrieve value of the default product price as string
     *
     * @return string
     */
    public function getDefaultProductPriceString()
    {
        return (string) $this->_coreConfig->getValue(self::DEFAULT_PRODUCT_PRICE_CONFIG_PATH, 'default');
    }
}

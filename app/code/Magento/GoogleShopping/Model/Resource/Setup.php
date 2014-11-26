<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Resource;

class Setup extends \Magento\Framework\Module\DataSetup
{
    /**
     * @var \Magento\GoogleShopping\Helper\Data
     */
    protected $_googleShoppingData;

    /**
     * @var \Magento\GoogleShopping\Model\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\GoogleShopping\Model\ConfigFactory $configFactory
     * @param \Magento\GoogleShopping\Helper\Data $googleShoppingData
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        \Magento\GoogleShopping\Model\ConfigFactory $configFactory,
        \Magento\GoogleShopping\Helper\Data $googleShoppingData,
        $moduleName = 'Magento_GoogleShopping',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_configFactory = $configFactory;
        $this->_googleShoppingData = $googleShoppingData;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\GoogleShopping\Helper\Data
     */
    public function getGoogleShoppingData()
    {
        return $this->_googleShoppingData;
    }
}

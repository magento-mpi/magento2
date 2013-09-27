<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var null
     */
    protected $_googleShoppingData = null;

    /**
     * Config factory
     *
     * @var \Magento\GoogleShopping\Model\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\GoogleShopping\Model\ConfigFactory $configFactory
     * @param \Magento\GoogleShopping\Helper\Data $googleShoppingData
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\GoogleShopping\Model\ConfigFactory $configFactory,
        \Magento\GoogleShopping\Helper\Data $googleShoppingData,
        $resourceName,
        $moduleName = 'Magento_GoogleShopping',
        $connectionName = ''
    ) {
        $this->_configFactory = $configFactory;
        $this->_googleShoppingData = $googleShoppingData;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return null
     */
    public function getGoogleShoppingData()
    {
        return $this->_googleShoppingData;
    }
}

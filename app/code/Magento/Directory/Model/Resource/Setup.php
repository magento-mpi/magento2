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
 * Directory Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model\Resource;

class Setup extends \Magento\Module\Setup
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData;

    /**
     * @param \Magento\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Module\Setup\Context $context,
        $resourceName,
        \Magento\Directory\Helper\Data $directoryData,
        $moduleName = 'Magento_Directory',
        $connectionName = ''
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\Directory\Helper\Data
     */
    public function getDirectoryData()
    {
        return $this->_directoryData;
    }
}

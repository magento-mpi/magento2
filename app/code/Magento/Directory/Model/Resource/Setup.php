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

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Directory\Helper\Data $directoryData,
        $resourceName,
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

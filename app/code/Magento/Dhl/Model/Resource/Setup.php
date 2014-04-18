<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Dhl\Model\Resource;

class Setup extends \Magento\Module\Setup
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Module\Setup\Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Module\Setup\Context $context,
        $resourceName,
        $moduleName,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        $connectionName = \Magento\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_localeLists = $localeLists;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\Framework\Locale\ListsInterface
     */
    public function getLocaleLists()
    {
        return $this->_localeLists;
    }
}

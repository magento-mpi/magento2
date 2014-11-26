<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Resource;

/**
 * Resource Setup Model
 */
class Setup extends \Magento\Framework\Module\DataSetup
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        $moduleName = 'Magento_VersionsCms',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_coreDate = $coreDate;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }
}

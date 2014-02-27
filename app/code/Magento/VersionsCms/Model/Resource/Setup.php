<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Resource;

/**
 * Resource Setup Model
 */
class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\Enterprise\Model\Resource\Setup\MigrationFactory
     */
    protected $_entMigrationFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $entMigrationFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $entMigrationFactory,
        $moduleName = 'Magento_VersionsCms',
        $connectionName = ''
    ) {
        $this->_coreDate = $coreDate;
        $this->_entMigrationFactory = $entMigrationFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }
}

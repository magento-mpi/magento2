<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource;

/**
 * Module setup
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Setup extends \Magento\Framework\Module\Setup
{
    /**
     * Call afterApplyAllUpdates flag
     *
     * @var bool
     */
    protected $_callAfterApplyAllUpdates = true;

    /**
     * Synchronizer instance
     *
     * @var \Magento\SalesArchive\Model\Resource\Synchronizer
     */
    protected $_synchronizer;

    /**
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        $moduleName = 'Magento_SalesArchive',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_synchronizer = new \Magento\SalesArchive\Model\Resource\Synchronizer($this);
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * Run each time after applying of all updates,
     * if setup model setted  $_callAfterApplyAllUpdates flag to true
     *
     * @return $this
     */
    public function afterApplyAllUpdates()
    {
        $this->_synchronizer->syncArchiveStructure();
        return $this;
    }
}

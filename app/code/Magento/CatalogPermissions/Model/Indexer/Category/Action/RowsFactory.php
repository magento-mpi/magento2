<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

/**
 * Factory class for \Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows
 */
class RowsFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\CatalogPermissions\Model\Indexer\AbstractAction
     */
    public function create(array $data = array())
    {
        /** @var \Magento\CatalogPermissions\Model\Indexer\AbstractAction $instance */
        $instance = $this->objectManager->create($this->instanceName, $data);
        if (!$instance instanceof \Magento\CatalogPermissions\Model\Indexer\AbstractAction) {
            throw new \InvalidArgumentException(
                $this->instanceName . ' is not instance of \Magento\CatalogPermissions\Model\Indexer\AbstractAction'
            );
        }
        return $instance;
    }
}

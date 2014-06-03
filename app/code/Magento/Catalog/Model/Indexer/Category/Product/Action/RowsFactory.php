<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

/**
 * Factory class for \Magento\Catalog\Model\Indexer\Category\Product\Action\Rows
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
        $instanceName = 'Magento\Catalog\Model\Indexer\Category\Product\Action\Rows'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction
     */
    public function create(array $data = array())
    {
        /** @var \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction $instance */
        $instance = $this->objectManager->create($this->instanceName, $data);
        if (!$instance instanceof \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction) {
            throw new \InvalidArgumentException(
                $this->instanceName .
                ' is not instance of \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction'
            );
        }
        return $instance;
    }
}

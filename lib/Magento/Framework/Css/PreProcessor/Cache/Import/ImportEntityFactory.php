<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Cache\Import;

/**
 * Import entity factory
 */
class ImportEntityFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntity'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @param \Magento\Less\PreProcessor\File\Less $lessFile
     * @return ImportEntityInterface
     * @throws \InvalidArgumentException
     */
    public function create($lessFile)
    {
        /** @var \Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntityInterface $importEntity */
        $importEntity = $this->objectManager->create($this->instanceName, array('lessFile' => $lessFile));
        if (!$importEntity instanceof \Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntityInterface) {
            throw new \InvalidArgumentException(
                'Import Entity does not implement \Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntityInterface'
            );
        }
        return $importEntity;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

/**
 * Import entity factory
 */
class ImportEntityFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Css\PreProcessor\Cache\Import\ImportEntity'
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
        /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface $importEntity */
        $importEntity = $this->objectManager->create($this->instanceName, array('lessFile' => $lessFile));
        if (!$importEntity instanceof \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface) {
            throw new \InvalidArgumentException(
                'Import Entity does not implement \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface'
            );
        }
        return $importEntity;
    }
}

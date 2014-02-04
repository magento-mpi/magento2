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
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $filePath
     * @param array $params
     * @return \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface
     * @throws \Exception
     */
    public function create($filePath, $params)
    {
        /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface $importEntity */
        $importEntity = $this->objectManager->create(
            'Magento\Css\PreProcessor\Cache\Import\ImportEntity',
            array('filePath' => $filePath, 'params' => $params)
        );

        if (!$importEntity instanceof \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface) {
            throw new \Exception(
                'Import Entity does not implement \Magento\Css\PreProcessor\Cache\Import\ImportEntityInterface'
            );
        }

        return $importEntity;
    }
}



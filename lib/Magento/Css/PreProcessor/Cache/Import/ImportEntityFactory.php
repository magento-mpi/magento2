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
     * @return \Magento\Css\PreProcessor\Cache\Import\ImportEntity
     */
    public function create($filePath, $params)
    {
        return $this->objectManager->create(
            'Magento\Css\PreProcessor\Cache\Import\ImportEntity',
            array('filePath' => $filePath, 'params' => $params)
        );
    }
}



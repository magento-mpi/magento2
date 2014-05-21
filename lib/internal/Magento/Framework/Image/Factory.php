<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Image;

use Magento\Framework\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @param ObjectManager $objectManager
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(ObjectManager $objectManager, AdapterFactory $adapterFactory)
    {
        $this->objectManager = $objectManager;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * Create instance of \Magento\Framework\Image
     *
     * @param string|null $fileName
     * @param string|null $adapterName
     * @return \Magento\Framework\Image
     */
    public function create($fileName = null, $adapterName = null)
    {
        $adapter = $this->adapterFactory->create($adapterName);
        return $this->objectManager->create('Magento\Framework\Image', array('adapter' => $adapter, 'fileName' => $fileName));
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider;

use Magento\Framework\ObjectManager;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create data provider
     *
     * @param string $providerClass
     * @param array $arguments
     * @return mixed
     */
    public function create($providerClass, array $arguments = [])
    {
        return $this->objectManager->create($providerClass, ['arguments' => $arguments]);
    }
}

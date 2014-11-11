<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Control;

use Magento\Framework\ObjectManager;

/**
 * Class ButtonProviderFactory
 * @package Magento\Ui\DataProvider
 */
class ButtonProviderFactory
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
     * Create Button Provider
     *
     * @param string $providerClass
     * @param array $arguments
     * @return ButtonProviderInterface
     */
    public function create($providerClass, array $arguments = [])
    {
        $object = $this->objectManager->create($providerClass, ['arguments' => $arguments]);
        if (!$object instanceof ButtonProviderInterface) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must implement the interface ButtonProviderInterface.', $providerClass)
            );
        }
        return $object;
    }
}

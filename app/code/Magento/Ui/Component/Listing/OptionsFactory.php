<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Listing;

use Magento\Framework\ObjectManager;

/**
 * Class OptionsFactory
 */
class OptionsFactory
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
     * Getting provider object
     *
     * @param string $class
     * @param array $arguments
     * @return OptionsInterface
     * @throws \InvalidArgumentException
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        if (!($object instanceof OptionsInterface)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must implement the interface \Magento\Ui\Component\Listing\OptionsInterface', $class)
            );
        }

        return $object;
    }
}

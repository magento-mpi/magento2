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
 * Class RowPool
 */
class RowPool
{
    /**
     * @var RowInterface[]
     */
    protected $classPool;

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
     * @return RowInterface
     * @throws \InvalidArgumentException
     */
    public function get($class, array $arguments = [])
    {
        if (!isset($this->classPool[$class])) {
            $this->classPool[$class] = $this->objectManager->create($class, $arguments);
            if (!($this->classPool[$class] instanceof RowInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" must implement the interface \Magento\Ui\Component\Listing\RowInterface', $class)
                );
            }
        }

        return $this->classPool[$class];
    }
}

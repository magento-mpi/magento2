<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

/**
 * Class ReaderFactory
 */
class ReaderFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create reader instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\View\Layout\ReaderInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $reader = $this->objectManager->create($className, $data);
        if (!$reader instanceof \Magento\Framework\View\Layout\ReaderInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Framework\View\Layout\ReaderInterface'
            );
        }
        return $reader;
    }
}

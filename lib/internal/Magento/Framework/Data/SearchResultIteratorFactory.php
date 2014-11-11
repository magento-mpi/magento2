<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Class SearchResultIteratorFactory
 */
class SearchResultIteratorFactory
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
     * Create SearchResultIterator object
     *
     * @param string $className
     * @param array $arguments
     * @return SearchResultIterator
     * @throws \Magento\Framework\Exception
     */
    public function create($className, array $arguments = [])
    {
        $resultIterator = $this->objectManager->create($className, $arguments);
        if (!$resultIterator instanceof \Traversable) {
            throw new \Magento\Framework\Exception(
                $className . ' should be an iterator'
            );
        }
        return $resultIterator;
    }
}

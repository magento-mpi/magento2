<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB;

/**
 * Class QueryFactory
 */
class QueryFactory
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
     * Create Query object
     *
     * @param string $className
     * @param array $arguments
     * @return QueryInterface
     * @throws \Magento\Framework\Exception
     */
    public function create($className, array $arguments = [])
    {
        $query = $this->objectManager->create($className, $arguments);
        if (!$query instanceof QueryInterface) {
            throw new \Magento\Framework\Exception($className . ' doesn\'t implement \Magento\Framework\DB\QueryInterface');
        }
        return $query;
    }
}

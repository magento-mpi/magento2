<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\ObjectManager;
use Magento\Framework\Model\Exception;

class Repository
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array
     */
    private $algorithms = [];

    /**
     * @var AlgorithmInterface[]
     */
    private $instances = [];

    /**
     * Construct
     *
     * @param ObjectManager $objectManager
     * @param array $algorithms
     */
    public function __construct(ObjectManager $objectManager, array $algorithms)
    {
        $this->objectManager = $objectManager;
        $this->algorithms = $algorithms;
    }

    /**
     * Create algorithm
     *
     * @param string $algorithmType
     * @param array $data
     * @throws Exception
     * @return AlgorithmInterface
     */
    public function get($algorithmType, array $data = [])
    {
        if (!isset($this->instances[$algorithmType])) {
            if (!isset($this->algorithms[$algorithmType])) {
                throw new Exception($algorithmType . ' was not found in algorithms');
            }

            $className = $this->algorithms[$algorithmType];
            $model = $this->objectManager->create($className, $data);

            if (!$model instanceof AlgorithmInterface) {
                throw new Exception(
                    $className . ' doesn\'t extends \Magento\Framework\Search\Dynamic\Algorithm\AlgorithmInterface'
                );
            }
            $this->instances[$algorithmType] = $model;
        }

        return $this->instances[$algorithmType];
    }
}

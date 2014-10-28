<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Dynamic;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Exception;
use Magento\Framework\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class AlgorithmFactory
{
    /**
     * XML configuration path for Price Layered Navigation
     */
    const XML_PATH_RANGE_CALCULATION = 'catalog/layered_navigation/price_range_calculation';

    const RANGE_CALCULATION_AUTO = 'auto';
    const RANGE_CALCULATION_IMPROVED = 'improved';
    const RANGE_CALCULATION_MANUAL = 'manual';

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $algorithms;

    /**
     * Construct
     *
     * @param ObjectManager $objectManager
     * @param ScopeConfigInterface $scopeConfig
     * @param array $algorithms
     */
    public function __construct(ObjectManager $objectManager, ScopeConfigInterface $scopeConfig, array $algorithms)
    {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->algorithms = $algorithms;
    }

    /**
     * Create algorithm
     *
     * @param array $data
     * @return AlgorithmInterface
     * @throws Exception
     */
    public function create(array $data = [])
    {
        $calculationType = $this->scopeConfig->getValue(self::XML_PATH_RANGE_CALCULATION, ScopeInterface::SCOPE_STORE);

        if (!isset($this->algorithms[$calculationType])) {
            throw new Exception($calculationType . ' doesn\'t found in algorithms');
        }

        $className = $this->algorithms[$calculationType];
        $model = $this->objectManager->create($className, $data);

        if (!$model instanceof AlgorithmInterface) {
            throw new Exception($className . ' doesn\'t extends \Magento\Framework\Model\Exception');
        }

        return $model;
    }
}

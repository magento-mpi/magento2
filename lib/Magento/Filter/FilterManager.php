<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Magento Filter Manager
 *
 * @method string email(string $value)
 * @method string money(string $value, $params)
 * @method string simple(string $value, $params)
 * @method string object(string $value, $params)
 * @method string sprintf(string $value, $params)
 * @method string template(string $value, $params)
 * @method string arrayFilter(string $value)
 * @method string removeAccents(string $value, $params)
 * @method string splitWords(string $value, $params)
 * @method string removeTags(string $value, $params)
 * @method string stripTags(string $value, $params)
 * @method string truncate(string $value, $params)
 * @method string encrypt(string $value, $params)
 * @method string decrypt(string $value, $params)
 */
class FilterManager
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var FilterManager\Config
     */
    protected $config;

    /**
     * @var FactoryInterface[]
     */
    protected $factoryInstances;

    /**
     * @param \Magento\ObjectManager $objectManger
     * @param FilterManager\Config $config
     */
    public function __construct(
        \Magento\ObjectManager $objectManger,
        FilterManager\Config $config
    ) {
        $this->objectManager = $objectManger;
        $this->config = $config;
    }

    /**
     * Get filter object
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     * @throws \UnexpectedValueException
     */
    public function get($filterAlias, array $arguments = array())
    {
        $filter = $this->createFilterInstance($filterAlias, $arguments);
        if (!$filter instanceof \Zend_Filter_Interface) {
            throw new \UnexpectedValueException('Filter object must implement Zend_Filter_Interface interface, '
                . get_class($filter) . ' was given.');
        }
        return $filter;
    }

    /**
     * Create filter instance
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     * @throws \InvalidArgumentException
     */
    protected function createFilterInstance($filterAlias, $arguments)
    {
        /** @var FactoryInterface $factory */
        foreach ($this->getFilterFactories() as $factory) {
            if ($factory->canCreateFilter($filterAlias)) {
                return $factory->createFilter($filterAlias, $arguments);
            }
        }
        throw new \InvalidArgumentException('Filter was not found by given alias ' . $filterAlias);
    }

    /**
     * Get registered factories
     *
     * @return FactoryInterface[]
     * @throws \UnexpectedValueException
     */
    protected function getFilterFactories()
    {
        if (null === $this->factoryInstances) {
            foreach ($this->config->getFactories() as $class) {
                $factory = $this->objectManager->create($class);
                if (!$factory instanceof FactoryInterface) {
                    throw new \UnexpectedValueException(
                        'Filter factory must implement FilterFactoryInterface interface, '
                            . get_class($factory) . ' was given.'
                    );
                }
                $this->factoryInstances[] = $factory;
            }
        }
        return $this->factoryInstances;
    }

    /**
     * Create filter and filer value
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     */
    public function __call($filterAlias, array $arguments = array())
    {
        $value = array_shift($arguments);
        if (count($arguments)) {
            $arguments = array_shift($arguments);
            if (!is_array($arguments)) {
                $arguments = array($arguments);
            }
        }
        return $this->createFilterInstance($filterAlias, $arguments)->filter($value);
    }
}

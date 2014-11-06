<?php
    /**
     * {license_notice}
     *
     * @copyright {copyright}
     * @license   {license_link}
     */
namespace Magento\Framework\Interception\ObjectManager;

class Factory implements \Magento\Framework\ObjectManager\Factory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Object manager config
     *
     * @var \Magento\Framework\ObjectManager\Config
     */
    protected $config;

    /**
     * Definition list
     *
     * @var \Magento\Framework\ObjectManager\Definition
     */
    protected $definitions;

    /**
     * @param \Magento\Framework\ObjectManager\Config $config
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\ObjectManager\Definition $definitions
     * @param array $globalArguments
     */
    public function __construct(
        \Magento\Framework\ObjectManager\Config $config,
        \Magento\Framework\ObjectManager $objectManager = null,
        \Magento\Framework\ObjectManager\Definition $definitions = null,
        $globalArguments = array()
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->definitions = $definitions ?: new \Magento\Framework\ObjectManager\Definition\Runtime();
        $this->globalArguments = $globalArguments;
        $this->interceptionConfig = $interceptionConfig;
    }

    /**
     * Set object manager
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Parse array argument
     *
     * @param array $array
     * @return void
     */
    protected function parseArray(&$array)
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                if (isset($item['instance'])) {
                    $itemType = $item['instance'];
                    $isShared = (isset($item['shared'])) ? $item['shared'] : $this->config->isShared($itemType);
                    $array[$key] = $isShared
                        ? $this->objectManager->get($itemType)
                        : $this->objectManager->create($itemType);
                } elseif (isset($item['argument'])) {
                    $array[$key] = isset($this->globalArguments[$item['argument']])
                        ? $this->globalArguments[$item['argument']]
                        : null;
                } else {
                    $this->parseArray($array[$key]);
                }
            }
        }
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create($requestedType, array $runtimeArguments = array())
    {
        $type = $this->config->getInstanceType($requestedType);
        $arguments = $this->config->getArguments($type);
        if ($arguments == null) {
            return new $type();
        }
        $args = array();
        if (count($runtimeArguments)) {
            $arguments = array_replace($arguments, $runtimeArguments);
        }
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                if (isset($argument['__ins__'])) {
                    $args[] = (isset($argument['__shared__']) ? $argument['__shared__'] : false)
                        ? $this->objectManager->get($argument['__ins__'])
                        : $this->objectManager->create($argument['__ins__']);
                } else if (isset($argument['__arg__'])) {
                    $argKey = $argument['__arg__'];
                    $args[] =
                        isset($this->globalArguments[$argKey]) ? $this->globalArguments[$argKey] : $argument['__def__'];
                } else if (isset($argument['__val__'])) {
                    $args[] = $argument['__val__'];
                } else if (!empty($argument)) {
                    $this->parseArray($argument);
                    $args[] = $argument;
                }
            } else {
                $args[] = $this->objectManager->get($argument);
            }
        }

        $reflection = new \ReflectionClass($type);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Set global arguments
     *
     * @param array $arguments
     * @return void
     */
    public function setArguments($arguments)
    {
        $this->globalArguments = $arguments;
    }
}

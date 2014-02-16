<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View;

use Magento\View\Element\BlockFactory;

/**
 * Class DataSourcePool
 */
class DataSourcePool
{
    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var array
     */
    protected $dataSources = array();

    /**
     * @var array
     */
    protected $assignments = array();

    /**
     * @param \Magento\View\Element\BlockFactory $blockFactory
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param string $name
     * @param string $class
     * @return object
     * @throws \Exception
     */
    public function add($name, $class)
    {
        if (!isset($this->dataSources[$name])) {

            if (!class_exists($class)) {
                throw new \Exception(__('Invalid Data Source class name: ' . $class));
            }

            $data = $this->blockFactory->createBlock($class);

            $this->dataSources[$name] = $data;
        }

        return $this->dataSources[$name];
    }

    /**
     * @param null $name
     * @return array|object|null
     */
    public function get($name = null)
    {
        if (!isset($name)) {
            return $this->dataSources;
        }

        return isset($this->dataSources[$name]) ? $this->dataSources[$name] : null;
    }

    /**
     * @param $dataName
     * @param $namespace
     * @param $alias
     */
    public function assign($dataName, $namespace, $alias)
    {
        $alias = $alias ?: $dataName;
        $data = $this->get($dataName);

        $this->assignments[$namespace][$alias] = $data;
    }

    /**
     * @param $namespace
     * @return array
     */
    public function getNamespaceData($namespace)
    {
        return isset($this->assignments[$namespace]) ? $this->assignments[$namespace] : array();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Dependency;

use Magento\Data\Graph;

/**
 * Class Circular
 */
class Circular
{
    /**
     * Map where the key is the vertex and the value are the adjacent vertices(dependencies) of this vertex
     *
     * @var array
     */
    protected $dependencies = array();

    /**
     * Modules circular dependencies map
     *
     * @var array
     */
    protected $circularDependencies = array();

    /**
     * Graph object
     *
     * @var \Magento\Data\Graph
     */
    protected $graph;

    /**
     * Build modules dependencies
     *
     * @param array $dependencies Key is the vertex and the value are the adjacent vertices(dependencies) of this vertex
     * @return array
     */
    public function buildModulesDependencies($dependencies)
    {
        $this->init($dependencies);

        foreach (array_keys($this->dependencies) as $vertex) {
            $this->expandDependencies($vertex);
        }

        $circulars = $this->graph->findCycle(null, false);
        foreach ($circulars as $circular) {
            array_shift($circular);
            $this->buildCircular($circular);
        }

        return $this->divideByModules($this->circularDependencies);
    }

    /**
     * Init data before building
     *
     * @param array $dependencies
     */
    protected function init($dependencies)
    {
        $this->dependencies = $dependencies;
        $this->circularDependencies = array();
        $this->graph = new Graph(array_keys($this->dependencies), array());
    }

    /**
     * Expand modules dependencies from chain
     *
     * @param string $vertex
     * @param array $path nesting path
     */
    protected function expandDependencies($vertex, $path = array())
    {
        if (!$this->dependencies[$vertex]) {
            return;
        }

        $path[] = $vertex;
        foreach ($this->dependencies[$vertex] as $dependency) {
            $relations = $this->graph->getRelations();
            if (isset($relations[$vertex][$dependency])) {
                continue;
            }
            $this->graph->addRelation($vertex, $dependency);

            $searchResult = array_search($dependency, $path);

            if (false !== $searchResult) {
                $this->buildCircular(array_slice($path, $searchResult));
                break;
            } else {
                $this->expandDependencies($dependency, $path);
            }
        }
    }

    /**
     * Build all circular dependencies based on chain
     *
     * @param array $modules
     */
    protected function buildCircular($modules)
    {
        $path = '/' . implode('/', $modules);
        if (isset($this->circularDependencies[$path])) {
            return;
        }
        $this->circularDependencies[$path] = $modules;
        array_push($modules, array_shift($modules));
        $this->buildCircular($modules);
    }

    /**
     * Divide dependencies by modules
     *
     * @param array $circularDependencies
     * @return array
     */
    protected function divideByModules($circularDependencies)
    {
        $dependenciesByModule = array();
        foreach ($circularDependencies as $circularDependency) {
            $module = $circularDependency[0];
            array_push($circularDependency, $module);
            $dependenciesByModule[$module][] = $circularDependency;
        }

        return $dependenciesByModule;
    }
}

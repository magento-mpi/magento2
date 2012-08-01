<?php

use Zend\Di\Di;

class Magento_ObjectManager_Zend extends Magento_ObjectManager_ObjectManagerAbstract
{
    /**
     * @var \Zend\Di\Di
     */
    protected $_di;

    /**
     * @var string
     */
    protected $_compileDir;

    /**
     * @var string
     */
    protected $_moduleDir;

    /**
     * @param Zend\Di\Di $di
     * @param string $compileDir
     */
    public function __construct(\Zend\Di\Di $di, $moduleDir, $compileDir)
    {
        $this->_di = $di;
        $this->_compileDir = $compileDir;
        if (!file_exists($this->_compileDir)) {
            mkdir($this->_compileDir);
        }
        $this->_moduleDir = $moduleDir;
    }

    public function loadDefinitionsForClass($className)
    {
        $parts = explode('_', $className);
        $moduleName = $parts[0] . '_' . $parts[1];
        $this->loadDefinitions($moduleName);
    }

    public function loadDefinitions($moduleName)
    {
        if (!file_exists($this->_compileDir . '/' . $moduleName . '-definition.php')) {
            $strategy = new \Zend\Di\Definition\IntrospectionStrategy(new \Zend\Code\Annotation\AnnotationManager());
            $strategy->setMethodNameInclusionPatterns(array());
            $strategy->setInterfaceInjectionInclusionPatterns(array());
            $definition = new \Zend\Di\Definition\CompilerDefinition($strategy);
            $definition->addDirectory($this->_moduleDir . '/' . str_replace('_', '/', $moduleName));
            $controllerPath = $this->_moduleDir . '/' . str_replace('_', '/', $moduleName) . '/controllers/';
            if (file_exists($controllerPath)) {
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath)) as $file) {
                    if (! $file->isDir()) {
                        require_once $file->getPathname();
                    }
                }
            }
            $definition->compile();

            file_put_contents(
                $this->_compileDir . '/' . $moduleName . '-definition.php',
                '<?php return ' . var_export($definition->toArrayDefinition()->toArray(), true) . ';'
            );
        }
        $this->_di->definitions()->addDefinition(
            new \Zend\Di\Definition\ArrayDefinition( require $this->_compileDir . '/' . $moduleName . '-definition.php')
        );
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function create($className, array $arguments = array())
    {
        if (!$this->_di->definitions()->hasClass($className)) {
            $this->loadDefinitionsForClass($className);
        }
        $ni =  $this->_di->newInstance($className, $arguments);
        return $ni;
    }

    /**
     * Retreive cached object instance
     *
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array())
    {
        if (!$this->_di->definitions()->hasClass($className)) {
            $this->loadDefinitionsForClass($className);
        }
        $ni = $this->_di->get($className, $arguments);
        return $ni;

    }

    /**
     * @param string $class
     * @param array $parameters
     */
    public function setParameters($class, array $parameters)
    {
        $this->_di->instanceManager()->setParameters($class, $parameters);
    }
}

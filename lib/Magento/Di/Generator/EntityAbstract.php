<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Di_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'abstract';

    /**
     * @var array
     */
    private $_errors = array();

    /**
     * Source model class name
     *
     * @var string
     */
    private $_sourceClassName;

    /**
     * Result model class name
     *
     * @var string
     */
    private $_resultClassName;

    /**
     * @var Magento_Di_Generator_Io
     */
    private $_ioObject;

    /**
     * Autoloader instance
     *
     * @var Magento_Autoload
     */
    private $_autoloader;

    /**
     * Class generator object
     *
     * @var Magento_Di_Generator_CodeGenerator_Interface
     */
    protected $_classGenerator;

    /**
     * @param string $sourceClassName
     * @param string $resultClassName
     * @param Magento_Di_Generator_Io $ioObject
     * @param Magento_Di_Generator_CodeGenerator_Interface $classGenerator
     * @param Magento_Autoload $autoloader
     */
    public function __construct(
        $sourceClassName = null,
        $resultClassName = null,
        Magento_Di_Generator_Io $ioObject = null,
        Magento_Di_Generator_CodeGenerator_Interface $classGenerator = null,
        Magento_Autoload $autoloader = null
    ) {
        $this->_sourceClassName = $sourceClassName;

        if ($resultClassName) {
            $this->_resultClassName = $resultClassName;
        } elseif ($sourceClassName) {
            $this->_resultClassName = $this->_getDefaultResultClassName($sourceClassName);
        }

        if ($ioObject) {
            $this->_ioObject = $ioObject;
        } else {
            $this->_ioObject = new Magento_Di_Generator_Io();
        }

        if ($classGenerator) {
            $this->_classGenerator = $classGenerator;
        } else {
            $this->_classGenerator = new Magento_Di_Generator_CodeGenerator_Zend();
        }

        if ($autoloader) {
            $this->_autoloader = $autoloader;
        } else {
            $this->_autoloader = Magento_Autoload::getInstance();
        }
    }

    /**
     * Generation template method
     *
     * @return bool
     */
    public function generate()
    {
        try {
            if ($this->_validateData()) {
                $sourceCode = $this->_generateCode();
                if ($sourceCode) {
                    $fileName = $this->_ioObject->getResultFileName($this->_getResultClassName());
                    $this->_ioObject->writeResultFile($fileName, $sourceCode);
                    return true;
                } else {
                    $this->_addError('Can\'t generate source code.');
                }
            }
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
        }
        return false;
    }

    /**
     * List of occurred generation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $className
     * @return Magento_Di_Generator_EntityAbstract
     */
    public function setSourceClassName($className)
    {
        $this->_sourceClassName = $className;
        return $this;
    }

    /**
     * @param string $className
     * @return Magento_Di_Generator_EntityAbstract
     */
    public function setResultClassName($className)
    {
        $this->_resultClassName = $className;
        return $this;
    }

    /**
     * @return string
     */
    protected function _getSourceClassName()
    {
        return $this->_sourceClassName;
    }

    /**
     * @return string
     */
    protected function _getResultClassName()
    {
        return $this->_resultClassName;
    }

    /**
     * @param string $modelClassName
     * @return string
     */
    protected function _getDefaultResultClassName($modelClassName)
    {
        return $modelClassName . ucfirst(static::ENTITY_TYPE);
    }

    /**
     * Returns list of properties for class generator
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        // const CLASS_NAME = '<source_class_name>';
        $className = array(
            'name'         => 'CLASS_NAME',
            'const'        => true,
            'defaultValue' => $this->_getSourceClassName(),
            'docblock'     => array('shortDescription' => 'Entity class name'),
        );

        // protected $_objectManager = null;
        $objectManager = array(
            'name'       => '_objectManager',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Object Manager instance',
                'tags'             => array(
                    array('name' => 'var', 'description' => 'Magento_ObjectManager')
                )
            ),
        );

        return array($className, $objectManager);
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        // public function __construct(Magento_ObjectManager $objectManager)
        return array(
            'name'       => '__construct',
            'parameters' => array(
                array('name' => 'objectManager', 'type' => 'Magento_ObjectManager'),
            ),
            'body' => '$this->_objectManager = $objectManager;',
            'docblock' => array(
                'shortDescription' => 'Factory constructor',
                'tags'             => array(
                    array(
                        'name'        => 'param',
                        'description' => 'Magento_ObjectManager $objectManager'
                    ),
                ),
            ),
        );
    }

    /**
     * Returns list of methods for class generator
     *
     * @return mixed
     */
    abstract protected function _getClassMethods();

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator
            ->setName($this->_getResultClassName())
            ->setProperties($this->_getClassProperties())
            ->setMethods($this->_getClassMethods())
            ->setDocblock($this->_getClassDocBlock());

        return $this->_getGeneratedCode();
    }

    /**
     * @param string $message
     * @return Magento_Di_Generator_EntityAbstract
     */
    protected function _addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    /**
     * @return bool
     */
    protected function _validateData()
    {
        $sourceClassName = $this->_getSourceClassName();
        $resultClassName = $this->_getResultClassName();
        $resultFileName = $this->_ioObject->getResultFileName($resultClassName);

        if (!$this->_autoloader->classExists($sourceClassName)) {
            $this->_addError('Source class ' . $sourceClassName . ' doesn\'t exist.');
            return false;
        } elseif ($this->_autoloader->classExists($resultClassName)) {
            $this->_addError('Result class ' . $resultClassName . ' already exists.');
            return false;
        } elseif (!$this->_ioObject->makeGenerationDirectory()) {
            $this->_addError('Can\'t create directory ' . $this->_ioObject->getGenerationDirectory() . '.');
            return false;
        } elseif (!$this->_ioObject->makeResultFileDirectory($resultClassName)) {
            $this->_addError(
                'Can\'t create directory ' . $this->_ioObject->getResultFileDirectory($resultClassName) . '.'
            );
            return false;
        } elseif ($this->_ioObject->fileExists($resultFileName)) {
            $this->_addError('Result file ' . $resultFileName . ' already exists.');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    protected function _getClassDocBlock()
    {
        $description = ucfirst(static::ENTITY_TYPE) . ' class for ' . $this->_getSourceClassName();
        return array('shortDescription' => $description);
    }

    /**
     * @return string
     */
    protected function _getGeneratedCode()
    {
        $sourceCode = $this->_classGenerator->generate();
        return $this->_fixCodeStyle($sourceCode);
    }

    /**
     * @param string $sourceCode
     * @return mixed
     */
    protected function _fixCodeStyle($sourceCode)
    {
        $sourceCode = str_replace(' array (', ' array(', $sourceCode);
        $sourceCode = preg_replace("/{\n+/m", "{\n", $sourceCode);
        $sourceCode = preg_replace("/\n+}/m", "\n}", $sourceCode);
        return $sourceCode;
    }
}

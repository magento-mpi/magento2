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
     * Default code generation directory
     */
    const DEFAULT_DIRECTORY = 'var/generation';

    /**
     * Directory permission for created directories
     */
    const DIRECTORY_PERMISSION = 0777;

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
     * File directory of result file
     *
     * @var string
     */
    private $_resultFileDirectory;

    /**
     * Result file name
     *
     * @var string
     */
    private $_resultFileName;

    /**
     * Path to directory where new file must be created
     *
     * @var string
     */
    private $_generationDirectory;

    /**
     * Autoloader instance
     *
     * @var Magento_Autoload
     */
    private $_autoloader;

    /**
     * Class generator object
     *
     * @var Zend_CodeGenerator_Php_Class
     */
    protected $_classGenerator;

    /**
     * @param string $sourceClassName
     * @param string $resultClassName
     * @param string $generationDirectory
     * @param Zend_CodeGenerator_Php_Class $classGenerator
     * @param Magento_Autoload $autoloader
     */
    public function __construct(
        $sourceClassName = null,
        $resultClassName = null,
        $generationDirectory = null,
        Zend_CodeGenerator_Php_Class $classGenerator = null,
        Magento_Autoload $autoloader = null
    ) {
        $this->_sourceClassName = $sourceClassName;

        if ($resultClassName) {
            $this->_resultClassName = $resultClassName;
        } elseif ($sourceClassName) {
            $this->_resultClassName = $this->_getDefaultResultClassName($sourceClassName);
        }

        if ($generationDirectory) {
            $this->_generationDirectory = rtrim($generationDirectory, DS) . DS;
        } else {
            $this->_generationDirectory = BP . DS . self::DEFAULT_DIRECTORY . DS;
        }

        if ($classGenerator) {
            $this->_classGenerator = $classGenerator;
        } else {
            $this->_classGenerator = new Zend_CodeGenerator_Php_Class();
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
                    $this->_writeResultFile($sourceCode);
                    return true;
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
     * Generates default class name for result file
     *
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
    abstract protected function _getClassProperties();

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
        if (!$this->_autoloader->classExists($this->_getSourceClassName())) {
            $this->_addError('Source class ' . $this->_getSourceClassName() . ' doesn\'t exist.');
            return false;
        } elseif ($this->_autoloader->classExists($this->_resultClassName)) {
            $this->_addError('Result class ' . $this->_resultClassName . ' already exists.');
            return false;
        } elseif (!$this->_makeDirectory($this->_generationDirectory)) {
            return false;
        } elseif (!$this->_makeDirectory($this->_getResultFileDirectory())) {
            return false;
        } elseif (file_exists($this->_getResultFileName())) {
            $this->_addError('Result file ' . $this->_getResultFileName() . ' already exists.');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    protected function _getClassDocBlock()
    {
        $this->_classGenerator->setDocblock(array('shortDescription' => '{license_notice}'));
        $classDocBlock = $this->_classGenerator->getDocblock();

        $classNameParts = explode('_', $this->_getResultClassName());
        unset($classNameParts[count($classNameParts) - 1]);
        if (isset($classNameParts[0])) {
            $classDocBlock->setTag(array(
                'name'        => 'category',
                'description' => ' ' . $classNameParts[0]
            ));
            if (isset($classNameParts[1])) {
                $classDocBlock->setTag(array(
                    'name'        => 'package',
                    'description' => '  ' . $classNameParts[0] . '_' . $classNameParts[1]
                ));
            }
        }

        $classDocBlock->setTags(array(
            array('name' => 'copyright', 'description' => '{copyright}'),
            array('name' => 'license', 'description' => '  {license_link}'),
        ));

        return $classDocBlock;
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

    /**
     * @return string
     */
    private function _getResultFileDirectory()
    {
        if (is_null($this->_resultFileDirectory)) {
            $classParts = explode('_', $this->_resultClassName);
            unset($classParts[count($classParts) - 1]);
            $this->_resultFileDirectory = $this->_generationDirectory . implode(DS, $classParts) . DS;
        }
        return $this->_resultFileDirectory;
    }

    /**
     * @return string
     */
    private function _getResultFileName()
    {
        if (is_null($this->_resultFileName)) {
            $resultFileName = str_replace('_', DS, $this->_resultClassName);
            $this->_resultFileName = $this->_generationDirectory . $resultFileName . '.php';
        }
        return $this->_resultFileName;
    }

    /**
     * @param $content
     * @return bool
     */
    private function _writeResultFile($content)
    {
        $content = "<?php\n" . $content;
        return file_put_contents($this->_getResultFileName(), $content) !== false;
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function _makeDirectory($directory)
    {
        if (is_dir($directory)) {
            if (!is_writable($directory)) {
                $this->_addError('Directory ' . $directory . ' is not writable.');
                return false;
            }
        } elseif (!@mkdir($directory, self::DIRECTORY_PERMISSION, true)) {
            $this->_addError('Can\'t create directory ' . $directory . '.');
            return false;
        }
        return true;
    }
}

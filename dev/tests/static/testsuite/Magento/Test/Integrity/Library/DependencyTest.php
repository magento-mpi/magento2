<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library;

use Magento\TestFramework\Utility\Files;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\FileReflection;
use Zend\Code\Reflection\ParameterReflection;

/**
 * @package Magento\Test\Integrity\Dependency
 */
class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $throws = array();

    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * @var array
     */
    protected $staticCalls = array();

    /**
     * @var array
     */
    protected $uses = array();

    /**
     * @var bool
     */
    protected $parseUse = false;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Fix for one of our class in library implemented interface from application
     *
     * @TODO: remove this code when class Magento\Data\Collection will fixed
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__ . '/../../../../../../../../app/code/Magento/Core/Model/Option/ArrayInterface.php';
    }

    /**
     * Test check injectable dependencies in library
     *
     * @test
     * @dataProvider libraryDataProvider
     */
    public function testCheckDependencies($file)
    {
        include_once $file;
        $fileReflection = new FileReflection($file);

        $this->tokens = token_get_all($fileReflection->getContents());
        $this->parseContent();

        $this->checkInjectableDependencies($fileReflection);
        $this->checkStaticCallDependencies($fileReflection);
        $this->checkThrowsDependencies($fileReflection);

        if ($this->hasErrors()) {
            $this->fail($this->getFailMessage());
        }
    }

    /**
     * Detect wrong dependencies
     *
     * @param ParameterReflection $parameter
     * @param ClassReflection $class
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function detectWrongDependencies(ParameterReflection $parameter, ClassReflection $class)
    {
        try {
            $parameter->getClass();
        } catch (\ReflectionException $e) {
            $this->addError($e, $class->getName());
        }
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->errors     = array();
        $this->tokens     = array();
        $this->uses       = array();
        $this->staticCalls = array();
    }

    /**
     * Check if file has wrong dependencies
     *
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Prepare failed message
     *
     * @return string
     */
    protected function getFailMessage()
    {
        $failMessage = '';
        foreach ($this->errors as $class => $dependencies) {
            $failMessage .= $class . ' depends for non-library '
                . (count($dependencies) > 1 ? 'classes ' : 'class ');
            foreach ($dependencies as $dependency) {
                $failMessage .= $dependency . ' ';
            }
            $failMessage = trim($failMessage) . PHP_EOL;
        }
        return $failMessage;
    }

    /**
     * Contains all library files
     *
     * @return array
     */
    public function libraryDataProvider()
    {
        return Files::init()->getClassFiles(false, false, false, false, false, true, true);
    }

    /**
     * @param FileReflection $fileReflection
     */
    protected function checkInjectableDependencies(FileReflection $fileReflection)
    {
        foreach ($fileReflection->getClasses() as $class) {
            /** @var ClassReflection $class */
            foreach ($class->getMethods() as $method) {
                /** @var \Zend\Code\Reflection\MethodReflection $method */
                foreach ($method->getParameters() as $parameter) {
                    /** @var ParameterReflection $parameter */
                    $this->detectWrongDependencies($parameter, $class);
                }
            }
        }
    }

    /**
     * @param FileReflection $fileReflection
     * @throws \ReflectionException
     */
    protected function checkStaticCallDependencies(FileReflection $fileReflection)
    {
        foreach ($this->staticCalls as $staticCall) {
            $class = $this->getClassByStaticCall($staticCall);
            if ($this->hasUses() && !$this->isGlobalClass($class)) {
                $class = $this->prepareFullClassName($class);
            } elseif (!$this->isMagentoClass($class)) {
                continue;
            }
            try {
                new ClassReflection($class);
            } catch (\ReflectionException $e) {
                $this->addError($e, $fileReflection->getFileName());
            }
        }
    }

    /**
     * @param FileReflection $fileReflection
     */
    protected function checkThrowsDependencies(FileReflection $fileReflection)
    {
        foreach ($this->throws as $throw) {
            $class = '';
            if ($this->tokens[$throw + 2][0] == T_NEW) {
                $step = 4;
                while ($this->isString($this->tokens[$throw+$step][0])
                    || $this->isNamespaceSeparator($this->tokens[$throw+$step][0])
                ) {
                    $class .= trim($this->tokens[$throw + $step][1]);
                    $step++;
                }

                if ($this->hasUses() && !$this->isGlobalClass($class)) {
                    $class = $this->prepareFullClassName($class);
                }

                try {
                    new ClassReflection($class);
                } catch (\ReflectionException $e) {
                    $this->addError($e, $fileReflection->getFileName());
                }
            }
        }
    }

    /**
     * @param string $class
     * @return string
     */
    protected function prepareFullClassName($class)
    {
        preg_match('#^([A-Za-z0-9_]+)(.*)$#', $class, $match);
        foreach ($this->uses as $use) {
            if (preg_match('#^([^\s]+)\s+as\s+(.*)$#', $use, $useMatch) && $useMatch[2] == $match[1]) {
                $class = $useMatch[1] . $match[2];
                break;
            }
            $packages = explode('\\', $use);
            end($packages);
            $lastPackageKey = key($packages);
            if ($packages[$lastPackageKey] == $match[1]) {
                $class = $use . $match[2];
            }
        }
        return $class;
    }

    /**
     * @param \ReflectionException $exception
     * @param string $key
     * @throws \ReflectionException
     */
    protected function addError($exception, $key)
    {
        if (preg_match('#^Class ([A-Za-z\\\\]+) does not exist$#', $exception->getMessage(), $result)) {
            $this->errors[$key][] = $result[1];
        } else {
            throw $exception;
        }
    }

    /**
     * @param string $class
     * @return int
     */
    public function isMagentoClass($class)
    {
        return preg_match('#^\\\\Magento\\\\#', $class);
    }

    /**
     * @param string $class
     * @return int
     */
    protected function isGlobalClass($class)
    {
        return preg_match('#^\\\\#', $class);
    }

    /**
     * @param int $staticCall
     * @return string
     */
    protected function getClassByStaticCall($staticCall)
    {
        $step = 1;
        $staticClassParts = array();
        while ($this->isString($this->tokens[$staticCall-$step][0])
            || $this->isNamespaceSeparator($this->tokens[$staticCall-$step][0])
        ) {
            $staticClassParts[] = $this->tokens[$staticCall-$step][1];
            $step++;
        }
        return implode(array_reverse($staticClassParts));
    }

    protected function parseContent()
    {
        foreach ($this->tokens as $key => $token) {
            $this->parseUses($token);
            $this->parseStaticCall($token, $key);
            $this->parseThrows($token, $key);
        }
    }

    /**
     * @param array|string $token
     * @param $key
     */
    protected function parseThrows($token, $key)
    {
        if ($this->hasTokenCode($token) && $this->isThrow($token[0])) {
            $this->throws[] = $key;
        }
    }

    /**
     * @param array|string $token
     */
    protected function parseUses($token)
    {
        if ($this->hasTokenCode($token)) {
            if ($this->isParseUseInProgress()) {
                $this->appendToLastUses($token[1]);
            }
            if ($this->isUse($token[0])) {
                $this->startParseUse();
                $this->addNewUses();
            }
        } else {
            if ($this->isParseUseInProgress()) {
                if ($token == ';') {
                    $this->stopParseUse();
                }
                if ($token == ',') {
                    $this->addNewUses();
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function hasUses()
    {
        return !empty($this->uses);
    }

    /**
     * @param string|array $token
     * @param int $key
     */
    protected function parseStaticCall($token, $key)
    {
        if ($this->hasTokenCode($token)
            && $this->isStaticCall($token[0])
            && $this->isTokenClass($this->getPreviousToken($key))
        ) {
            $this->staticCalls[] = $key;
        }
    }

    /**
     * @param array $token
     * @return bool
     */
    protected function isTokenClass($token)
    {
        return $this->hasTokenCode($token)
            && !(in_array($token[1], array('self', 'parent')) || preg_match('#^\$#', $token[1]));
    }

    /**
     * @param int $key
     * @param int $step
     * @return array
     */
    protected function getPreviousToken($key, $step = 1)
    {
        return $this->tokens[$key - $step];
    }

    /**
     * @param array|string $token
     * @return bool
     */
    protected function hasTokenCode($token)
    {
        return is_array($token);
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isThrow($code)
    {
        return $code == T_THROW;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isUse($code)
    {
        return $code == T_USE;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isString($code)
    {
        return $code == T_STRING;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isNamespaceSeparator($code)
    {
        return $code == T_NS_SEPARATOR;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isStaticCall($code)
    {
        return $code == T_PAAMAYIM_NEKUDOTAYIM;
    }

    /**
     * @return bool
     */
    protected function isParseUseInProgress()
    {
        return $this->parseUse;
    }

    protected function stopParseUse()
    {
        $this->parseUse = false;
    }

    protected function startParseUse()
    {
        $this->parseUse = true;
    }

    protected function addNewUses()
    {
        $this->uses[] = '';
    }

    /**
     * @param string $value
     */
    protected function appendToLastUses($value)
    {
        end($this->uses);
        $this->uses[key($this->uses)] .= trim($value);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library;

use Magento\TestFramework\Integrity\Library\Injectable;
use Magento\TestFramework\Integrity\Library\PhpParser\ParserFactory;
use Magento\TestFramework\Integrity\Library\PhpParser\Tokens;
use Magento\TestFramework\Utility\Files;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\FileReflection;

/**
 * @package Magento\Test
 */
class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var Tokens
     */
    protected $tokens = array();

    /**
     * @var Injectable
     */
    protected $injectable;

    /**
     * @var
     */
    protected $fileReflection;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->injectable = new Injectable();
    }

    /**
     * Test check dependencies in library from application
     *
     * @test
     * @dataProvider libraryDataProvider
     */
    public function testCheckDependencies($file)
    {
        $fileReflection = new FileReflection($file);
        $this->tokens   = new Tokens($fileReflection->getContents(), new ParserFactory());
        $this->tokens->parseContent();

        $exceptions = array();
        foreach ($this->tokens->getDependencies() as $dependency) {
            try {
                new ClassReflection($dependency);
            } catch (\ReflectionException $e) {
                $exceptions[] = $e;
            }
        }

        $this->injectable->checkDependencies($fileReflection);

        foreach (array_merge($exceptions, $this->injectable->getDependencies()) as $exception) {
            $this->addError($exception, $fileReflection->getFileName());
        }

        if ($this->hasErrors()) {
            $this->fail($this->getFailMessage());
        }
    }

    /**
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->errors = array();
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
        // @TODO: remove this code when class Magento\Data\Collection will fixed
        include_once __DIR__ . '/../../../../../../../../app/code/Magento/Core/Model/Option/ArrayInterface.php';
        $blackList = file(__DIR__ . DIRECTORY_SEPARATOR . '_files/blacklist.txt', FILE_IGNORE_NEW_LINES);
        $dataProvider = Files::init()->getClassFiles(false, false, false, false, false, true, true);

        foreach ($dataProvider as $key => $data) {
            include_once $data[0];
            $file = str_replace(realpath(__DIR__ . '/../../../../../../../../') . '/', '', $data[0]);
            if (in_array($file, $blackList)) {
                unset($dataProvider[$key]);
            }
        }
        return $dataProvider;
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
}

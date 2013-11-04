<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library;

use Magento\TestFramework\Integrity\Library\Injectable;
use Magento\TestFramework\Integrity\Library\PhpParser\Tokens;
use Magento\TestFramework\Integrity\Library\PhpParser\UseToken;
use Magento\TestFramework\Integrity\Library\PhpParser\StaticCallToken;
use Magento\TestFramework\Integrity\Library\PhpParser\ThrowToken;
use Magento\TestFramework\Utility\Files;
use Zend\Code\Reflection\FileReflection;

/**
 * @package Magento\Test\Integrity\Dependency
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
     * @var UseToken
     */
    protected $useToken;

    /**
     * @var StaticCallToken
     */
    protected $staticCallToken;

    /**
     * @var ThrowToken
     */
    protected $throwToken;

    /**
     * @var Injectable
     */
    protected $injectable;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->tokens =          new Tokens();
        $this->useToken =        new UseToken();
        $this->staticCallToken = new StaticCallToken($this->tokens, $this->useToken);
        $this->throwToken =      new ThrowToken($this->tokens, $this->useToken);
        $this->injectable =      new Injectable();
    }

    /**
     * Test check injectable dependencies in library
     *
     * @test
     * @dataProvider libraryDataProvider
     */
    public function testCheckDependencies($file)
    {
        $fileReflection = new FileReflection($file);

        $this->tokens->parse($fileReflection->getContents());
        $this->parseContent();

        $this->injectable->checkDependencies($fileReflection);
        $this->throwToken->checkDependencies();
        $this->staticCallToken->checkDependencies();

        foreach ($this->getExceptions() as $exception) {
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
     * @return \ReflectionException[]
     */
    public function getExceptions()
    {
        return array_merge(
            $this->injectable->getDependencies(),
            $this->staticCallToken->getDependencies(),
            $this->throwToken->getDependencies()
        );
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

    protected function parseContent()
    {
        foreach ($this->tokens->getAllTokens() as $key => $token) {
            $this->useToken->parseUses($token);
            $this->staticCallToken->parseStaticCall($token, $key);
            $this->throwToken->parseThrows($token, $key);
        }
    }
}

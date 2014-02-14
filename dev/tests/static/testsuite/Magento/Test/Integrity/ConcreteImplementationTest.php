<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity;

/**
 * Scan source code for dependency of blacklisted classes
 */
class ConcreteImplementationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Classes that should not be injected as dependency in app code
     *
     * @var array
     */
    protected static $_classesBlacklist = null;

    public function testWrongConcreteImplementation()
    {
        self::$_classesBlacklist = file(__DIR__ . '/_files/classes/blacklist.txt', FILE_IGNORE_NEW_LINES);

        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             */
            function ($file) {
                $content = file_get_contents($file);

                if (strpos($content, "namespace Magento\Core") !== false) {
                    return;
                }

                $result = (bool)preg_match(
                    '/function __construct\(([^\)]*)\)/iS',
                    $content,
                    $matches
                );
                if ($result && !empty($matches[1])) {
                    $arguments = explode(',', $matches[1]);
                    foreach ($arguments as $argument) {
                        $type = explode(' ', trim($argument));
                        if (in_array(trim($type[0]), self::$_classesBlacklist)) {
                            $this->fail("Incorrect class dependency found in $file:" . trim($type[0]));
                        }
                    }
                }
            },
            \Magento\TestFramework\Utility\Files::init()->getClassFiles(true, false, false, false, false, false)
        );
    }
}

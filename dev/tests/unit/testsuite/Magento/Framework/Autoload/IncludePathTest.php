<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Autoload;

class IncludePathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_originalPath = '';

    public static function setUpBeforeClass()
    {
        self::$_originalPath = get_include_path();
    }

    protected function tearDown()
    {
        set_include_path(self::$_originalPath);
    }

    /**
     * @param string $class
     * @param bool string|$expectedValue
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($class, $expectedValue)
    {
        $this->assertFalse((new \Magento\Framework\Autoload\IncludePath())->getFile($class));
        (new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/_files');
        $this->assertEquals($expectedValue, (new \Magento\Framework\Autoload\IncludePath())->getFile($class));
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        return array(
            array('TestClass', realpath(__DIR__ . '/_files/TestClass.php')),
            array('\Ns\TestClass', realpath(__DIR__ . '/_files/Ns/TestClass.php')),
            array('Non_Existing_Class', false)
        );
    }

    /**
     * @dataProvider addIncludePathDataProvider
     *
     * @param string|array $fixturePath
     * @param bool $prepend
     * @param string $expectedIncludePath
     */
    public function testAddIncludePath($fixturePath, $prepend, $expectedIncludePath)
    {
        $expectedIncludePath = str_replace('%include_path%', get_include_path(), $expectedIncludePath);
        $this->assertNotEquals($expectedIncludePath, get_include_path());
        (new \Magento\Framework\Autoload\IncludePath())->addIncludePath($fixturePath, $prepend);
        $this->assertEquals($expectedIncludePath, get_include_path());
    }

    public function addIncludePathDataProvider()
    {
        $pathSeparator = PATH_SEPARATOR;
        return array(
            'prepend string' => array('fixture_path', true, "fixture_path{$pathSeparator}%include_path%"),
            'prepend array' => array(
                array('fixture_path_one', 'fixture_path_two'),
                true,
                "fixture_path_one{$pathSeparator}fixture_path_two{$pathSeparator}%include_path%"
            ),
            'append string' => array('fixture_path', false, "%include_path%{$pathSeparator}fixture_path"),
            'append array' => array(
                array('fixture_path_one', 'fixture_path_two'),
                false,
                "%include_path%{$pathSeparator}fixture_path_one{$pathSeparator}fixture_path_two"
            )
        );
    }

    /**
     * @param string $class
     * @param string|bool $expectedValue
     * @dataProvider getFileDataProvider
     */
    public function testLoad($class, $expectedValue)
    {
        (new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/_files');
        $this->assertFalse(class_exists($class, false));
        (new \Magento\Framework\Autoload\IncludePath())->load($class);
        if ($expectedValue) {
            $this->assertTrue(class_exists($class, false));
        } else {
            $this->assertFalse(class_exists($class, false));
        }
    }

    public function testGetFilePath()
    {
        $original = '\Magento\Framework\ObjectManager\Factory\Factory';
        $result = 'Magento/Framework/ObjectManager/Factory/Factory.php';
        $this->assertEquals((new \Magento\Framework\Autoload\IncludePath())->getFilePath($original), $result);

        $original = 'Zend_Acl_Role_Registry_Exception';
        $result = 'Zend/Acl/Role/Registry/Exception.php';
        $this->assertEquals((new \Magento\Framework\Autoload\IncludePath())->getFilePath($original), $result);
    }
}

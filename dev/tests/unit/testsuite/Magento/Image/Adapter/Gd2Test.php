<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Image\Adapter;

use Magento\TestFramework\Helper\ObjectManager;


/**
 * Mocking crucial for this adapter global functions
 */

/**
 * @param $paramName
 * @throws \InvalidArgumentException
 * @return string
 */
function ini_get($paramName)
{
    if ('memory_limit' == $paramName) {
        return Gd2Test::$memoryLimit;
    }

    throw new \InvalidArgumentException('Unexpected parameter ' . $paramName);
}

/**
 * @param $file
 * @return mixed
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function getimagesize($file)
{
    return Gd2Test::$imageData;
}

/**
 * @param $real
 * @return int
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function memory_get_usage($real)
{
    return 1000000;
}

/**
 * @param $callable
 * @param $param
 * @return bool
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function call_user_func($callable, $param)
{
    return false;
}
/**
 * \Magento\Image\Adapter\Gd2 class test
 */
class Gd2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Value to mock ini_get('memory_limit')
     *
     * @var string
     */
    public static $memoryLimit;

    /**
     * @var array simulation of getimagesize()
     */
    public static $imageData = array();

    /**
     * Adapter for testing
     * @var \Magento\Image\Adapter\Gd2
     */
    protected $adapter;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup testing object
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->adapter = $this->objectManager->getObject('\Magento\Image\Adapter\Gd2');
    }

    /**
     * Test parent class
     */
    public function testParentClass()
    {
        $this->assertInstanceOf('\Magento\Image\Adapter\AbstractAdapter', $this->adapter);
    }

    /**
     * Test open() method
     *
     * @param $fileData array
     * @param $exception string|bool|null
     * @param $limit string
     * @dataProvider filesProvider
     */
    public function testOpen($fileData, $exception, $limit)
    {
        self::$memoryLimit = $limit;
        self::$imageData = $fileData;

        if (!empty($exception)) {
            $this->setExpectedException($exception);
        }

        $this->adapter->open('file');
    }

    public function filesProvider()
    {
        $smallFile = array(
            0 => 480,
            1 => 320,
            2 => 2,
            3 => 'width="480" height="320"',
            'bits' => 8,
            'channels' => 3,
            'mime' => 'image/jpeg'
        );

        $bigFile = array(
            0 => 3579,
            1 => 2398,
            2 => 2,
            3 => 'width="3579" height="2398"',
            'bits' => 8,
            'channels' => 3,
            'mime' => 'image/jpeg'
        );

        return array(
            'positive_M' => array($smallFile, false, '2M'),
            'positive_KB' => array($smallFile, false, '2048KB'),
            'negative_bytes' => array($bigFile, 'OverflowException', '2048000')
        );
    }
}

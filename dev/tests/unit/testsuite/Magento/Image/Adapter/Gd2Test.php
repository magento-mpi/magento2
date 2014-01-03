<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Image\Adapter;

use \Magento\TestFramework\Helper\ObjectManager;

/**
 * Mocking crucial for this adapter global functions
 */

/**
 * @param $paramName
 * @return string
 */
function ini_get($paramName) {
    if ('memory_limit' == $paramName) {
        return '2M';
    }

    return \ini_get($paramName);
}

/**
 * @param $file
 * @return mixed
 */
function getimagesize($file)
{
    return Gd2Test::${$file};
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
     * @var array simulation of getimagesize() output for small image
     */
    public static $smallFile = array(
        0 => 480,
        1 => 320,
        2 => 2,
        3 => 'width="480" height="320"',
        'bits' => 8,
        'channels' => 3,
        'mime' => 'image/jpeg',
    );

    /**
     * @var array simulation of getimagesize() output for big image
     */
    public static $bigFile = array(
        0 => 3579,
        1 => 2398,
        2 => 2,
        3 => 'width="3579" height="2398"',
        'bits' => 8,
        'channels' => 3,
        'mime' => 'image/jpeg',
    );
    
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
     * @dataProvider filesProvider
     */
    public function testOpen($filePath, $exception)
    {
        if (!empty($exception)) {
            $this->setExpectedException($exception);
        }

        $this->adapter->open($filePath);
    }

    public function filesProvider()
    {
        return array(
            'positive' => array('smallFile', false),
            'negative' => array('bigFile', 'OverflowException')
        );
    }
}

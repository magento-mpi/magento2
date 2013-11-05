<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\ClassName;

/**
 * @package Magento\Test
 */
class ClassNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMagentoClassesDataProvider
     * @test
     *
     * @param string $className
     * @param bool $result
     */
    public function testIsMagnetoClass($className, $result)
    {
        $this->assertEquals($result, (new ClassName($className))->isMagentoClass());
    }

    /**
     * @return array
     */
    public function getMagentoClassesDataProvider()
    {
        return array(
            'positive' => array('\Magento\Core\Model\AnyClass', true),
            'negative' => array('\NotMagento\Core\Model\AnyClass', false),
        );
    }

    /**
     * @dataProvider getGlobalClassesDataProvider
     * @test
     *
     * @param string $className
     * @param bool $result
     */
    public function testIsGlobalClass($className, $result)
    {
        $this->assertEquals($result, (new ClassName($className))->isGlobalClass());
    }

    /**
     * @return array
     */
    public function getGlobalClassesDataProvider()
    {
        return array(
            'positive' => array('\Exception', true),
            'negative' => array('Exception', false),
        );
    }
}
 
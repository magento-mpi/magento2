<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code;

class NameBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Code\NameBuilder
     */
    protected $nameBuilder;

    protected function setUp()
    {
        $nelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->nameBuilder = $nelper->getObject('Magento\Framework\Code\NameBuilder');
    }

    /**
     * @param array $parts
     * @param string $expected
     *
     * @dataProvider buildClassNameDataProvider
     */
    public function testBuildClassName($parts, $expected)
    {
        $this->assertEquals($expected, $this->nameBuilder->buildClassName($parts));
    }

    public function buildClassNameDataProvider()
    {
        return array(
            array(array('Checkout', 'Controller', 'Index'), 'Checkout\Controller\Index'),
            array(array('checkout', 'controller', 'index'), 'Checkout\Controller\Index'),
            array(
                array('Magento_Backend', 'Block', 'urlrewrite', 'edit', 'form'),
                'Magento\Backend\Block\Urlrewrite\Edit\Form'
            )
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\ParserFactory;

/**
 * @package Magento\Test
 */
class ParserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Integrity\Library\PhpParser\Tokens
     */
    protected $tokens;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->tokens = $this->getMockBuilder(
            'Magento\TestFramework\Integrity\Library\PhpParser\Tokens'
        )->disableOriginalConstructor()->getMock();
    }

    /**
     * Covered createParsers method
     *
     * @test
     */
    public function testCreateParsers()
    {
        $parseFactory = new ParserFactory();
        $parseFactory->createParsers($this->tokens);
        $this->assertInstanceOf('Magento\TestFramework\Integrity\Library\PhpParser\Uses', $parseFactory->getUses());
        $this->assertInstanceOf(
            'Magento\TestFramework\Integrity\Library\PhpParser\StaticCalls',
            $parseFactory->getStaticCalls()
        );
        $this->assertInstanceOf(
            'Magento\TestFramework\Integrity\Library\PhpParser\Throws',
            $parseFactory->getThrows()
        );
    }
}

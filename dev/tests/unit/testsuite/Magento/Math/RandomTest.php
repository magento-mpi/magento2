<?php
/**
 * Test \Magento\Math\Random
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Math;

class RandomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $length
     * @param string $chars
     *
     * @dataProvider getRandomStringDataProvider
     */
    public function testGetRandomString($length, $chars = null)
    {
        $mathRandom = new \Magento\Math\Random;
        $string = $mathRandom->getRandomString($length, $chars);

        $this->assertEquals($length, strlen($string));
        if ($chars !== null) {
            $this->_assertContainsOnlyChars($string, $chars);
        }
    }

    public function getRandomStringDataProvider()
    {
        return array(
            array(0),
            array(10),
            array(10, \Magento\Math\Random::CHARS_LOWERS),
            array(10, \Magento\Math\Random::CHARS_UPPERS),
            array(10, \Magento\Math\Random::CHARS_DIGITS),
            array(20,
                \Magento\Math\Random::CHARS_LOWERS
                    . \Magento\Math\Random::CHARS_UPPERS
                    . \Magento\Math\Random::CHARS_DIGITS
            ),
        );
    }

    public function testGetUniqueHash()
    {
        $mathRandom = new \Magento\Math\Random;
        $hashOne = $mathRandom->getUniqueHash();
        $hashTwo = $mathRandom->getUniqueHash();
        $this->assertTrue(is_string($hashOne));
        $this->assertTrue(is_string($hashTwo));
        $this->assertNotEquals($hashOne, $hashTwo);
    }

    /**
     * @param string $string
     * @param string $chars
     */
    protected function _assertContainsOnlyChars($string, $chars)
    {
        if (preg_match('/[^' . $chars . ']+/', $string, $matches)) {
            $this->fail(sprintf('Unexpected char "%s" found', $matches[0]));
        }
    }
}

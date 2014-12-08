<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\Language;

/**
 * Test for configuration of language
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $languageXml = file_get_contents(__DIR__ . '/_files/language.xml');
        $languageConfig = new Config($languageXml);
        $this->assertEquals('en_GB', $languageConfig->getCode());
        $this->assertEquals('magento', $languageConfig->getVendor());
        $this->assertEquals('en_gb', $languageConfig->getPackage());
        $this->assertEquals('100', $languageConfig->getSortOrder());
        $this->assertEquals(
            [
                ['vendor' => 'oxford-university', 'package' => 'en_us'],
                ['vendor' => 'oxford-university', 'package' => 'en_gb'],
            ],
            $languageConfig->getUses()
        );
    }
}

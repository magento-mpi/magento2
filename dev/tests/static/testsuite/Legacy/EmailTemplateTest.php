<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete directives in email templates
 */
class Legacy_EmailTemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider obsoleteDirectivesDataProvider
     */
    public function testObsoleteDirectives($file)
    {
        $suggestion = sprintf(Legacy_ObsoleteCodeTest::SUGGESTION_MESSAGE, '{{escapehtml}}');
        $this->assertNotRegExp(
            '/\{\{htmlescape.*?\}\}/i',
            file_get_contents($file),
            'Directive {{htmlescape}} is obsolete. ' . $suggestion
        );
    }

    public function obsoleteDirectivesDataProvider()
    {
        return Utility_Files::init()->getEmailTemplates();
    }
}

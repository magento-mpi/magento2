<?php
/**
 * {license_notice}
 *
 * Run a twig syntax checker
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Twig;

class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider listAllTwigFiles
     */
    public function testTwigTemplateValid($file, $runTest = true)
    {
        if (!$runTest) {
            return;
        }
        exec('php ' . __DIR__ . "/_files/twig-lint.phar lint $file", $output, $returnVar);
        $this->assertEquals(
            0, $returnVar,
            "File $file could not be validated by twig-lint.  The output is : \n" . implode("\n", $output)
        );
    }

    public function listAllTwigFiles()
    {
        $testData = \Magento\TestFramework\Utility\Files::composeDataSets(
            \Magento\TestFramework\Utility\Files::init()->getTwigFiles());
        if (empty($testData)) {
            $testData[] = array('Dummy data for when no twig files exist.', false);
        }
        return $testData;
    }
}

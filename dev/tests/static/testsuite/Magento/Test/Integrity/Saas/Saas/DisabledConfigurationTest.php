<?php
/**
 * Test which checks whether all disabled configuration options exist in system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Saas_Saas_DisabledConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDisabledConfigurationList()
    {
        $utility = Magento_TestFramework_Utility_Files::init();
        $files = $utility->getConfigFiles('adminhtml/system.xml', array(), false);

        $disabledOptions = include(
            $utility->getPathToSource() . '/app/code/Saas/Saas/Model/DisabledConfiguration/disabled_configuration.php'
        );

        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            foreach ($disabledOptions as $key => $path) {
                if ($xml->xpath($this->_buildXPath($path))) {
                    unset($disabledOptions[$key]);
                }
            }
        }

        $this->assertEmpty($disabledOptions, 'The following options do not exist in any of system.xml files: \'' .
            join(', ', $disabledOptions) . '\'');
    }

    /**
     * Build correct xpath for config file depending on $path structure
     *
     * @param $path
     * @return string
     */
    protected function _buildXPath($path)
    {
        $chunks = explode('/', $path);
        if (count($chunks) > 20) {
            $this->fail('Path \'' . $path . '\' has too many chunks');
        }

        if (isset($chunks[2])) {
            $fieldId = array_pop($chunks);
            $sectionId = array_shift($chunks);
            return '/config/system/section[@id="' . $sectionId . '"]/group[@id="'
                . implode('"]/group[@id="', $chunks) . '"]/field[@id="' . $fieldId . '"]';
        } elseif (isset($chunks[1])) {
            return '/config/system/section[@id="' . $chunks[0] . '"]/group[@id="' . $chunks[1] . '"]';
        } else {
            return '/config/system/section[@id="' . $chunks[0] . '"]';
        }
    }
}

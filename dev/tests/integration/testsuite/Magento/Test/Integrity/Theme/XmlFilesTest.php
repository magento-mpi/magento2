<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Theme;

class XmlFilesTest extends \PHPUnit_Framework_TestCase
{
    const NO_VIEW_XML_FILES_MARKER = 'no-view-xml';

    /**
     * @param string $file
     * @dataProvider viewConfigFileDataProvider
     */
    public function testViewConfigFile($file)
    {
        if ($file === self::NO_VIEW_XML_FILES_MARKER) {
            $this->markTestSkipped('No view.xml files in themes.');
        }
        $this->_validateConfigFile($file, \Mage::getBaseDir('lib') . '/Magento/Config/etc/view.xsd');
    }

    /**
     * @return array
     */
    public function viewConfigFileDataProvider()
    {
        $result = array();
        foreach (glob(\Mage::getBaseDir('design') . '/*/*/view.xml') as $file) {
            $result[$file] = array($file);
        }
        return $result === array() ? array(array(self::NO_VIEW_XML_FILES_MARKER)) : $result;
    }

    /**
     * @param string $themeDir
     * @dataProvider themeConfigFileExistsDataProvider
     */
    public function testThemeConfigFileExists($themeDir)
    {
        $this->assertFileExists($themeDir . '/theme.xml');
    }

    /**
     * @return array
     */
    public function themeConfigFileExistsDataProvider()
    {
        $result = array();
        foreach (glob(\Mage::getBaseDir('design') . '/*/*', GLOB_ONLYDIR) as $themeDir) {
            $result[$themeDir] = array($themeDir);
        }
        return $result;
    }

    /**
     * @param string $file
     * @dataProvider themeConfigFileDataProvider
     */
    public function testThemeConfigFileSchema($file)
    {
        $this->_validateConfigFile($file, \Mage::getBaseDir('lib') . '/Magento/Config/etc/theme.xsd');
    }

    /**
     * Configuration should declare a single package/theme that corresponds to the file system directories
     *
     * @param string $file
     * @dataProvider themeConfigFileDataProvider
     */
    public function testThemeConfigFileHasSingleTheme($file)
    {
        /** @var $configXml \SimpleXMLElement */
        $configXml = simplexml_load_file($file);
        $actualThemes = $configXml->xpath('/theme');
        $this->assertCount(1, $actualThemes, 'Single theme declaration is expected.');
    }

    /**
     * @return array
     */
    public function themeConfigFileDataProvider()
    {
        $result = array();
        foreach (glob(\Mage::getBaseDir('design') . '/*/*/theme.xml') as $file) {
            $result[$file] = array($file);
        }
        return $result;
    }

    /**
     * Perform test whether a configuration file is valid
     *
     * @param string $file
     * @param string $schemaFile
     * @throws \PHPUnit_Framework_AssertionFailedError if file is invalid
     */
    protected function _validateConfigFile($file, $schemaFile)
    {
        $domConfig = new \Magento\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }
}

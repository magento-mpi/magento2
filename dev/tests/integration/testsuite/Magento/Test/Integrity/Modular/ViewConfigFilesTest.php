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

namespace Magento\Test\Integrity\Modular;

class ViewConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider viewConfigFileDataProvider
     */
    public function testViewConfigFile($file)
    {
        $domConfig = new \Magento\Config\Dom($file);
        $result = $domConfig->validate(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Filesystem')->getPath(\Magento\Filesystem::LIB)
                . '/Magento/Config/etc/view.xsd',
            $errors
        );
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function viewConfigFileDataProvider()
    {
        $result = array();
        $files = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Module\Dir\Reader')
            ->getConfigurationFiles('view.xml');
        foreach ($files as $file) {
            $result[] = array($file);
        }
        return $result;
    }
}

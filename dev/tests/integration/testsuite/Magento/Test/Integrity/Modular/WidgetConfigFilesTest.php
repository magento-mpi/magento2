<?php
/**
 * Tests that existing widget.xml files are valid to schema individually and merged.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class WidgetConfigFilesTest extends \Magento\TestFramework\TestCase\AbstractConfigFiles
{
    /**
     * Returns the reader class name that will be instantiated via ObjectManager
     *
     * @return string reader class name
     */
    protected function _getReaderClassName()
    {
        return 'Magento\Widget\Model\Config\Reader';
    }

    /**
     * Returns a string that represents the path to the config file, starting in the app directory.
     *
     * Format is glob, so * is allowed.
     *
     * @return string
     */
    protected function _getConfigFilePathGlob()
    {
        return '/*/*/etc/widget.xml';
    }

    /**
     * Returns a path to the per file XSD file, relative to the modules directory.
     *
     * @return string
     */
    protected function _getXsdPath()
    {
        return '/Magento/Widget/etc/widget_file.xsd';
    }
}

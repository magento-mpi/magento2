<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Theme_Preview extends PHPUnit_Framework_TestCase
{
    /**
     * Get theme model
     *
     * @param array $data
     * @return Mage_Core_Model_Theme
     */
    protected function _getTheme($data)
    {
        return Mage::getModel('Mage_Core_Model_Theme', array($data));
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function themeDataFromConfiguration()
    {
        return array(array(
            'parent_id'            => null,
            'theme_path'           => 'default/iphone',
            'theme_version'        => '2.0.0.1',
            'theme_title'          => 'Iphone',
            'preview_image'        => 'images/preview.png',
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'is_featured'          => true,
            'theme_directory'      => implode(DIRECTORY_SEPARATOR,
                array(__DIR__, '_files', 'frontend', 'default', 'iphone')),
            'parent_theme_path'    => null,
            'area'                 => 'frontend',
        ));
    }
}

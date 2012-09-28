<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for filesystem themes collection
 */
class Mage_Core_Model_Theme_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test load themes collection from filesystem
     */
    public function testLoadThemesFromFileSystem()
    {
        $pathPattern = implode(DS, array(__DIR__, '..', '_files', 'design', 'frontend', 'default', '*', 'theme.xml'));

        /** @var $collection Mage_Core_Model_Theme_Collection */
        $collection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $collection->addTargetPattern($pathPattern);

        $this->assertEquals($collection->getItemsArray(), $this->_expectedThemeList());
    }

    /**
     * Expected theme list
     *
     * @return array
     */
    protected function _expectedThemeList()
    {
        return array(
            'default' => array(
                'theme_code'           => 'default',
                'theme_title'          => 'Default',
                'theme_version'        => '2.0.0.0',
                'parent_theme'         => null,
                'featured'             => true,
                'magento_version_from' => '2.0.0.0-dev1',
                'magento_version_to'   => '*',
                'theme_path'           => 'default/default',
                'preview_image'        => '',
                'theme_directory'      => implode(
                    DIRECTORY_SEPARATOR, array(__DIR__, '..', '_files', 'design', 'frontend', 'default', 'default')
                )
            ),
            'iphone' => array(
                'theme_code'           => 'iphone',
                'theme_title'          => 'Iphone',
                'theme_version'        => '2.0.0.0',
                'parent_theme'         => array('default', 'default'),
                'featured'             => false,
                'magento_version_from' => '2.0.0.0-dev1',
                'magento_version_to'   => '*',
                'theme_path'           => 'default/iphone',
                'preview_image'        => 'images/preview.png',
                'theme_directory'      => implode(
                    DIRECTORY_SEPARATOR, array(__DIR__, '..', '_files', 'design', 'frontend', 'default', 'iphone')
                )
            ),
        );
    }
}

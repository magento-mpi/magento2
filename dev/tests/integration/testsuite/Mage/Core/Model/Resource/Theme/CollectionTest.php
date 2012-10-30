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

class Mage_Core_Model_Resource_Theme_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testCollection()
    {
        $themeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeCollection->load();
        $oldTotalRecords = $themeCollection->getSize();
        foreach ($this->_themeList() as $themeData) {
            $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
            $themeModel->setData($themeData);
            $themeCollection->addItem($themeModel);
        }
        $themeCollection->save();
        $themes = $themeCollection->toArray();

        $newThemeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $newThemes = $newThemeCollection->toArray();

        $expectedTotalRecords = $oldTotalRecords + count($this->_themeList());
        $this->assertEquals($expectedTotalRecords, $newThemes['totalRecords']);
        $this->assertEquals($themes['items'], $newThemes['items']);
    }

    /**
     * Themes items
     *
     * @return array
     */
    protected function _themeList()
    {
        return array(
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/default',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test',
                'preview_image'        => 'test_default.jpg',
                'magento_version_from' => '2.0.0.0',
                'magento_version_to'   => '*',
                'is_featured'          => '1'
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/pro',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Professional Test',
                'preview_image'        => 'test_default.jpg',
                'magento_version_from' => '2.0.0.0',
                'magento_version_to'   => '*',
                'is_featured'          => '1'
            ),
        );
    }
}

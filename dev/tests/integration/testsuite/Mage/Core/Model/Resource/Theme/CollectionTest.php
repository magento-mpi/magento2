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
     * @dataProvider themeList
     * @magentoDbIsolation enabled
     */
    public function testCollection($themeList)
    {
        $themeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeCollection->load();
        $oldTotalRecords = $themeCollection->getSize();
        foreach ($themeList as $themeData) {
            $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
            $themeModel->setData($themeData);
            $themeCollection->addItem($themeModel);
        }
        $themeCollection->save();
        $themes = $themeCollection->toArray();

        $newThemeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $newThemes = $newThemeCollection->toArray();

        $expectedTotalRecords = $oldTotalRecords + count($themeList);
        $this->assertEquals($expectedTotalRecords, $newThemes['totalRecords']);
        $this->assertEquals($themes['items'], $newThemes['items']);
    }

    /**
     * @dataProvider themeList
     * @magentoDbIsolation enabled
     */
    public function testAddAreaFilter($themeList)
    {
        /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeCollection->load();
        foreach ($themeList as $themeData) {
            $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
            $themeModel->setData($themeData);
            $themeCollection->addItem($themeModel);
        }
        $themeCollection->save();

        /** @var $themeCollectionWithAreaFilter Mage_Core_Model_Resource_Theme_Collection */
        $themeCollectionWithAreaFilter = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeCollectionWithAreaFilter->addAreaFilter('custom_area');

        $this->assertEquals(count($themeList), count($themeCollectionWithAreaFilter));
    }

    /**
     * Themes items
     *
     * @return array
     */
    public function themeList()
    {
        return array(array(array(
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/default',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test',
                'preview_image'        => 'test_default.jpg',
                'magento_version_from' => '2.0.0.0',
                'magento_version_to'   => '*',
                'is_featured'          => '1',
                'area'                 => 'custom_area',
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/pro',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Professional Test',
                'preview_image'        => 'test_default.jpg',
                'magento_version_from' => '2.0.0.0',
                'magento_version_to'   => '*',
                'is_featured'          => '1',
                'area'                 => 'custom_area',
            ),
        )));
    }
}

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

namespace Magento\Core\Model\Resource\Theme;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    protected static function _getThemesCollection()
    {
        return  \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCollection()
    {
        $oldTotalRecords = self::_getThemesCollection()->getSize();

        $collection = $this->setThemeFixture();
        $oldThemes = $collection->toArray();

        $newThemeCollection = self::_getThemesCollection();
        $newThemes = $newThemeCollection->toArray();

        $expectedTotalRecords = $oldTotalRecords + count(self::getThemeList());
        $this->assertEquals($expectedTotalRecords, $newThemes['totalRecords']);
        $this->assertEquals($oldThemes['items'], $newThemes['items']);
    }

    /**
     * @param string $fullPath
     * @param bool $shouldExist
     * @magentoDataFixture setThemeFixture
     * @dataProvider getThemeByFullPathDataProvider
     */
    public function testGetThemeByFullPath($fullPath, $shouldExist)
    {
        $themeCollection = self::_getThemesCollection();
        $hasFound = false;
        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($themeCollection as $theme) {
            if ($theme->getFullPath() == $fullPath) {
                $hasFound = true;
                break;
            }
        }
        $message = $shouldExist ? 'Theme not found' : 'Theme is found but it should not' ;
        $this->assertEquals($shouldExist, $hasFound, $message);
    }

    /**
     * @return array
     */
    public function getThemeByFullPathDataProvider()
    {
        return array(
            array('test_area/test/default', true),
            array('test_area2/test/pro', true),
            array('test_area/test/pro', false),
            array('test_area2/test/default', false),
            array('', false),
            array('test_area', false),
            array('test_area/test', false),
            array('test_area/test/something', false),
        );
    }

    /**
     * @magentoDataFixture setThemeFixture
     * @magentoDbIsolation enabled
     * @dataProvider addAreaFilterDataProvider
     * @covers \Magento\Core\Model\Theme::addAreaFilter
     */
    public function testAddAreaFilter($area, $themeCount)
    {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
        $themeCollection->addAreaFilter($area);
        $this->assertCount($themeCount, $themeCollection);
    }

    /**
     * @return array
     */
    public function addAreaFilterDataProvider()
    {
        return array(
            array('area' => 'test_area', 'themeCount' => 1),
            array('area' => 'test_area2', 'themeCount' => 1),
            array('area' => 'test_area4', 'themeCount' => 0)
        );
    }

    /**
     * @magentoDataFixture setThemeFixture
     * @magentoDbIsolation enabled
     * @dataProvider addTypeFilterDataProvider
     * @covers \Magento\Core\Model\Theme::addTypeFilter
     */
    public function testAddTypeFilter($themeType, $themeCount)
    {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
        $themeCollection->addAreaFilter('test_area3');
        if ($themeType !== false) {
            $themeCollection->addTypeFilter($themeType);
        }
        $this->assertCount($themeCount, $themeCollection);
    }

    /**
     * @return array
     */
    public function addTypeFilterDataProvider()
    {
        return array(
            array('themeType' => \Magento\Core\Model\Theme::TYPE_PHYSICAL, 'themeCount' => 1),
            array('themeType' => \Magento\Core\Model\Theme::TYPE_VIRTUAL, 'themeCount' => 1),
            array('themeType' => \Magento\Core\Model\Theme::TYPE_STAGING, 'themeCount' => 1),
            array('themeType' => false, 'themeCount' => 3)
        );
    }

    /**
     * @magentoDataFixture setThemeFixture
     * @magentoDbIsolation enabled
     * @covers \Magento\Core\Model\Theme::filterVisibleThemes
     */
    public function testFilterVisibleThemes()
    {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
        $themeCollection->addAreaFilter('test_area3')->filterVisibleThemes();
        $this->assertCount(2, $themeCollection);
        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($themeCollection as $theme) {
            $this->assertTrue(in_array(
                $theme->getType(),
                array(\Magento\Core\Model\Theme::TYPE_PHYSICAL, \Magento\Core\Model\Theme::TYPE_VIRTUAL)
            ));
        }
    }

    /**
     * @magentoDataFixture setInheritedThemeFixture
     */
    public function testCheckParentInThemes()
    {
        $collection = self::_getThemesCollection(); //->checkParentInThemes();
        foreach (self::getInheritedThemeList() as $themeData) {
            $fullPath = $themeData['area'] . '/' . $themeData['theme_path'];
            $parentIdActual = $collection->clear()->getThemeByFullPath($fullPath)->getParentId();
            if ($themeData['parent_id']) {
                $parentFullPath = trim($themeData['parent_id'], '{}');
                $parentIdExpected = (int)$collection->clear()->getThemeByFullPath($parentFullPath)->getId();
                $this->assertEquals(
                    $parentIdActual,
                    $parentIdExpected,
                    sprintf('Invalid parent_id for theme "%s"', $fullPath)
                );
            } else {
                $parentIdExpected = 0;
                $this->assertEquals(
                    $parentIdExpected,
                    $parentIdActual,
                    sprintf('Parent id should be null for "%s"', $fullPath)
                );
            }
        }
    }

    /**
     * Set themes fixtures
     *
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public static function setThemeFixture()
    {
        $themeCollection = self::_getThemesCollection();
        $themeCollection->load();
        foreach (self::getThemeList() as $themeData) {
            /** @var $themeModel \Magento\View\Design\ThemeInterface */
            $themeModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\View\Design\ThemeInterface');
            $themeModel->setData($themeData);
            $themeCollection->addItem($themeModel);
        }
        return $themeCollection->save();
    }

    /**
     * @throws \Exception
     */
    public static function setInheritedThemeFixture()
    {
        $fixture = self::getInheritedThemeList();
        $idByPath = array();
        foreach ($fixture as $themeData) {
            /** @var $themeModel \Magento\View\Design\ThemeInterface */
            $themeModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\View\Design\ThemeInterface');
            $themeModel->setData($themeData);

            if ($themeData['parent_id'] && isset($idByPath[$themeData['parent_id']])) {
                $themeModel->setParentId($idByPath[$themeData['parent_id']]);
            }
            $themeModel->save();

            $idByPath[$themeModel->getFullPath()] = $themeModel->getId();
        }
    }

    /**
     * Get themes for making fixture
     *
     * @return array
     */
    public static function getThemeList()
    {
        return array(
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/default',
                'code'                 => 'test/default',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test',
                'preview_image'        => 'test_default.jpg',
                'is_featured'          => '1',
                'area'                 => 'test_area',
                'type'                 => \Magento\Core\Model\Theme::TYPE_PHYSICAL,
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/pro',
                'code'                 => 'test/pro',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Professional Test',
                'preview_image'        => 'test_default.jpg',
                'is_featured'          => '1',
                'area'                 => 'test_area2',
                'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL,
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/fixed1',
                'code'                 => 'test/fixed1',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Theme test 1',
                'preview_image'        => 'test_default.jpg',
                'is_featured'          => '1',
                'area'                 => 'test_area3',
                'type'                 => \Magento\Core\Model\Theme::TYPE_STAGING,
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/fixed2',
                'code'                 => 'test/fixed2',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Theme test 2',
                'preview_image'        => 'test_default.jpg',
                'is_featured'          => '1',
                'area'                 => 'test_area3',
                'type'                 => \Magento\Core\Model\Theme::TYPE_PHYSICAL,
            ),
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test/fixed3',
                'code'                 => 'test/fixed3',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Theme test 3',
                'preview_image'        => 'test_default.jpg',
                'is_featured'          => '1',
                'area'                 => 'test_area3',
                'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL,
            )
        );
    }

    /**
     * @return array
     */
    public static function getInheritedThemeList()
    {
        return array(
            array(
                'parent_id'            => '0',
                'theme_path'           => 'test1/test1',
                'code'                 => 'test1/test1',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test1',
                'preview_image'        => 'test1_test1.jpg',
                'is_featured'          => '1',
                'area'                 => 'area51',
                'type'                 => \Magento\Core\Model\Theme::TYPE_PHYSICAL
            ),
            array(
                'parent_id'            => 'area51/test1/test1',
                'theme_path'           => 'test1/test2',
                'code'                 => 'test1/test2',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test2',
                'preview_image'        => 'test1_test2.jpg',
                'is_featured'          => '1',
                'area'                 => 'area51',
                'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL
            ),
            array(
                'parent_id'            => 'area51/test1/test2',
                'theme_path'           => 'test1/test3',
                'code'                 => 'test1/test3',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test3',
                'preview_image'        => 'test1_test3.jpg',
                'is_featured'          => '1',
                'area'                 => 'area51',
                'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL
            ),
            array(
                'parent_id'            => 'area51/test1/test0',
                'theme_path'           => 'test1/test4',
                'code'                 => 'test1/test4',
                'theme_version'        => '2.0.0.0',
                'theme_title'          => 'Test4',
                'preview_image'        => 'test1_test4.jpg',
                'is_featured'          => '1',
                'area'                 => 'area51',
                'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL
            ),
        );
    }

    /**
     * @covers \Magento\Core\Model\Resource\Theme\Collection::filterPhysicalThemes
     */
    public function testFilterPhysicalThemesPerPage()
    {
        $collection = $this->_getThemesCollection();
        $collection->filterPhysicalThemes(1, \Magento\Core\Model\Resource\Theme\Collection::DEFAULT_PAGE_SIZE);

        $this->assertLessThanOrEqual(
            \Magento\Core\Model\Resource\Theme\Collection::DEFAULT_PAGE_SIZE, $collection->count()
        );

        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($collection as $theme) {
            $this->assertEquals(\Magento\Core\Model\App\Area::AREA_FRONTEND, $theme->getArea());
            $this->assertEquals(\Magento\Core\Model\Theme::TYPE_PHYSICAL, $theme->getType());
        }
    }

    /**
     * @covers \Magento\Core\Model\Resource\Theme\Collection::filterPhysicalThemes
     */
    public function testFilterPhysicalThemes()
    {
        $collection = $this->_getThemesCollection()->filterPhysicalThemes();

        $this->assertGreaterThan(0, $collection->count());

        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($collection as $theme) {
            $this->assertEquals(\Magento\Core\Model\App\Area::AREA_FRONTEND, $theme->getArea());
            $this->assertEquals(\Magento\Core\Model\Theme::TYPE_PHYSICAL, $theme->getType());
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Smart\Suite\Test\TestCase;

use Mtf\Fixture;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Smart\Suite\Test\EntityIterator;

class CrudTest extends Functional
{
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * @param Fixture $fixture
     *
     * @dataProvider dataProviderTestCreate
     */
    public function testManageEntities(Fixture $fixture)
    {
        $page = Factory::getPageFactory()->getSmartBackend();
        $page->openCreatePage($fixture);

        $formBlock = $page->getFormBlock($fixture);
        $formBlock->fill($fixture);
        $formBlock->save($fixture);
        $page->assertCreateResult($fixture);

        $page->openGridPage($fixture);
        $gridBlock = $page->getGridBlock($fixture);

        $filter = $this->getGridFilter($fixture);
        $gridBlock->searchAndOpen($filter);

        $formBlock = $page->getFormBlock($fixture);
        $formBlock->update($fixture);
        $formBlock->save($fixture);

        $page->assertUpdateResult($fixture);

        $page->openGridPage($fixture);

        $gridBlock = $page->getGridBlock($fixture);
        $gridBlock->deleteAll($filter);

        $page->assertDeleteResult($fixture);
    }

    /**
     * @return EntityIterator
     */
    public function dataProviderTestCreate()
    {
        //return new EntityIterator();
        return array(
            array(Factory::getFixtureFactory()->getMagentoCatalogProduct()->switchData('simple')),
            array(Factory::getFixtureFactory()->getMagentoCustomerCustomer()->switchData('backend_customer')),
            //array(Factory::getFixtureFactory()->getMagentoCmsPage()->switchData('cms_page'))
        );
    }

    /**
     * Get value for fill in filters in grid
     *
     * @param Fixture $fixture
     * @return array
     */
    protected function getGridFilter(Fixture $fixture)
    {
        $data = $fixture->getData();
        $dataConfig = $fixture->getDataConfig();
        $filter = array();
        if (isset($dataConfig['grid_filter'])) {
            foreach ($dataConfig['grid_filter'] as $field) {
                $filter[$field] = isset($data['fields'][$field]['value']) ? $data['fields'][$field]['value'] : null;
            }
        }

        return $filter;
    }
}

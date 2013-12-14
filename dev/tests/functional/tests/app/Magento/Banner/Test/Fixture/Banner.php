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

namespace Magento\Banner\Test\Fixture;

use Magento\Banner\Test\Repository\Banner as Repository;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Banner
 *
 * @package Magento\Banner\Test\Fixture
 */

class Banner extends DataFixture
{
    /**
     * Banner Id
     */
    private $id = null;

    /**
     * Create banner
     *
     * @return Banner
     */
    public function persist()
    {
        if ($this->id === null) {
            $this->id = Factory::getApp()->magentoBannerCreateBanner($this);
            $this->_data['fields']['banner_id']['value'] = $this->id;
        }
        else {
            Factory::getApp()->magentoBannerUpdateBanner($this);
        }
        return $this;
    }

    /**
     * Associate this banner with catalog price rule
     */
    public function relateCatalogPriceRule($catalogPriceRuleId)
    {
        $this->_data['fields']['in_banner_salesrule']['value'] = '1';
        $this->_data['fields']['banner_catalog_rules']['value'] = $catalogPriceRuleId;
    }

    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBannerBanner($this->_dataConfig, $this->_data);

        $this->switchData(Repository::TEXT_BANNER);
    }
}

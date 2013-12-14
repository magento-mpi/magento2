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

namespace Magento\Widget\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Magento\Widget\Test\Repository\Widget as Repository;

/**
 * Class Instance
 *
 * @package Magento\Widget\Test\Fixture
 */

class Widget extends DataFixture
{
    /**
     * Create Widget Instance
     *
     * @return Widget
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoWidgetCreateInstance($this);
        $this->_data['fields']['id'] = $id;
        return $this;
    }

    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoWidgetWidget($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData(Repository::BANNER_ROTATOR);
    }
}

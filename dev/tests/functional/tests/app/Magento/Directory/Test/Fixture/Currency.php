<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Directory\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

class Currency extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDirectoryCurrency($this->_dataConfig, $this->_data);
    }

    /**
     * {inheritdoc}
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoDirectoryAddCurrencyRate($this);
        $this->_data['fields']['id']['value'] = $id;

        return $this;
    }
}

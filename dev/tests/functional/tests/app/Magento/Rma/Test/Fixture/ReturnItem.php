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

namespace Magento\Rma\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Fixture with all necessary data for creating a return item on the frontend
 *
 * @package Magento\Rma\Test\Fixture
 */
class ReturnItem extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoRmaReturnItem($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('default');
    }

    /**
     * Get quantity to return
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->getData('fields/quantity');
    }

    /**
     * Get resolution of return
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->getData('fields/resolution');
    }

    /**
     * Get condition of return
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->getData('fields/condition');
    }

    /**
     * Get reason of return
     *
     * @return string
     */
    public function getReason()
    {
        return $this->getData('fields/reason');
    }
}

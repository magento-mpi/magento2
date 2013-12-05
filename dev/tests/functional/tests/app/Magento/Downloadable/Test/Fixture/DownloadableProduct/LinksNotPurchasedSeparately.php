<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Fixture\DownloadableProduct;

use Mtf\Factory\Factory;
use Magento\Downloadable\Test\Fixture\DownloadableProduct;

class LinksNotPurchasedSeparately extends LinksPurchasedSeparately
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();

        $this->_data = array_replace_recursive(
            $this->_data,
            [
                'fields' => [
                    'downloadable_link_purchase_type' => [
                        'value' => 'No',
                        'input_value' => '0',
                    ],
                ]
            ]
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDownloadableDownloadableProduct($this->_dataConfig, $this->_data);
    }
}

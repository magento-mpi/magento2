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

class LinksPurchasedSeparately extends DownloadableProduct
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
                        'value' => 'Yes',
                        'input_value' => '1',
                    ],
                    'downloadable' => [
                        'link' => [
                            [
                                'title' => ['value' => 'row1'],
                                'price' => ['value' => 2],
                                'number_of_downloads' => ['value' => 2],
                                'sample][type' => ['value' => 'url', 'input' => 'radio'],
                                'sample][url' => ['value' => 'http://example.com'],
                                'type' => ['value' => 'url', 'input' => 'radio'],
                                'link_url' => ['value' => 'http://example.com'],
                                'is_shareable' => [ // needed explicit default value for CURL handler
                                    'input' => 'select',
                                    'input_value' => static::LINK_IS_SHAREABLE_USE_CONFIG_VALUE,
                                ],
                            ]
                        ],
                        'group' => static::GROUP
                    ]
                ]
            ]
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDownloadableDownloadableProduct($this->_dataConfig, $this->_data);
    }
}

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

/**
 * Class LinksPurchasedSeparately
 *
 * Init downloadable data purchased separately
 */
class LinksPurchasedSeparately extends DownloadableProduct
{
    /**
     * Init downloadable data
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = array_replace_recursive(
            $this->_data,
            [
                'fields' => [
                    'downloadable_links' => [
                        'value' => [
                            'title' => 'Links%isolation%',
                            'links_purchased_separately' => 'Yes',
                            'downloadable' => [
                                'link' => [
                                    [
                                        'title' => 'row1%isolation%',
                                        'price' => 2.43,
                                        'number_of_downloads' => 2,
                                        'sample' => [
                                            'sample_type_url' => 'Yes',
                                            'sample_url' => 'http://example.com'
                                        ],
                                        'file_type_url' => 'Yes',
                                        'file_link_url' => 'http://example.com',
                                        'is_shareable' => 'No',
                                        'sort_order' => 0
                                    ]
                                ],
                            ]
                        ],
                        'group' => static::GROUP
                    ],
                ]
            ]
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDownloadableDownloadableProduct($this->_dataConfig, $this->_data);
    }
}

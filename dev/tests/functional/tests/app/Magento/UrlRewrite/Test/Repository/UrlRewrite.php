<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class UrlRewrite
 * Data for creation url rewrite
 */
class UrlRewrite extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'request_path' => 'test-test-test%isolation%.html',
            'target_path' => 'http://www.ebayinc.com/',
            'options' => 'Temporary (302)',
            'store_id' => 'Main Website/Main Website Store/Default Store View',
            'id_path' =>  ["test%isolation%"]
        ];

        $this->_data['default_without_target'] = [
            'request_path' => 'test-test-test%isolation%.html',
            'options' => 'Temporary (302)',
            'store_id' => 'Main Website/Main Website Store/Default Store View',
        ];

        $this->_data['custom_rewrite_wishlist'] = [
            'store_id' => 'Main Website/Main Website Store/Default Store View',
            'request_path' => 'wishlist/%isolation%',
            'target_path' => 'http://google.com',
            'options' => 'Temporary (302)',
            'description' => 'test description',
            'id_path' => ['entity' => "wishlist/%catalogProductSimple::100_dollar_product%"]
        ];
    }
}

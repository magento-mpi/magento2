<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Repository\Urlrewrite;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product
 * URL Rewrite Product Repository
 *
 * @package Magento\Backend\Test\Repository\Urlrewrite
 */
class Product extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => array(
                'url_rewrite_type' => 'For product',
                'fields' => array(
                    'request_path' => array(
                        'value' => '%rewritten_product_request_path%',
                    ),
                    'store' => array(
                        'value' => 'Default Store View'
                    ),
                ),
            ),
        );
        $this->_data['product_with_temporary_redirect'] = $this->_data['default'];
        $this->_data['product_with_temporary_redirect']['data']['fields']['redirect'] = array(
            'value' => 'Temporary (302)',
        );
    }
}

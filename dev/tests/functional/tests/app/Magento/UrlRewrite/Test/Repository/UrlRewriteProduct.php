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
 * Class UrlRewriteProduct
 * URL Rewrite Product Repository
 *
 */
class UrlRewriteProduct extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->_data['product_with_temporary_redirect']['data']['fields']['options'] = array(
            'value' => 'Temporary (302)',
        );
    }
}

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
 * Class Category
 * URL Rewrite Category Repository
 *
 * @package Magento\Backend\Test\Repository\Urlrewrite
 */
class Category extends AbstractRepository
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
                'fields' => array(
                    'request_path' => array(
                        'value' => '%rewritten_category_request_path%',
                    ),
                    'store' => array(
                        'value' => 'Default Store View',
                    ),
                ),
            ),
        );
        $this->_data['category_with_permanent_redirect'] = $this->_data['default'];
        $this->_data['category_with_permanent_redirect']['data']['fields']['redirect'] = array(
            'value' => 'Permanent (301)',
        );
    }
}

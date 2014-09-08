<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Paging;

use Magento\Ui\AbstractView;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * @var array
     */
    protected $viewConfiguration = [
        'config' => [
            'namespace' => 'cms.pages',
            'sizes' => [5, 10, 20, 30, 50, 100, 200],
            'params' => [
                'pageSize' => 5,
                'current' => 1
            ]
        ]
    ];
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Massaction;

use Magento\Ui\AbstractView;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * @var array
     */
    protected $configuration = [
        'actions' => [
            [
                'type' => 'removes',
                'title' => 'Delete'
            ],
            [
                'type' => 'updateAttributes',
                'title' => 'Update Attributes'
            ]
        ]
    ];
}

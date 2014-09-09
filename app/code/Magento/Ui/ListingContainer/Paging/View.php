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
            'sizes' => [5, 10, 20, 30, 50, 100, 200],
            'params' => [
                'pageSize' => 5,
                'current' => 1
            ]
        ]
    ];

    /**
     * @return array
     */
    public function getViewConfiguration()
    {
        $this->viewConfiguration['config']['name'] = $this->getLayout()->getBlock('listing')->getData('config/name');

        return parent::getViewConfiguration();
    }
}

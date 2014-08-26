<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogSearchEdit
 */
class CatalogSearchEdit extends BackendPage
{
    const MCA = 'catalog/search/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'form' => [
            'class' => 'Magento\CatalogSearch\Test\Block\Adminhtml\Edit\SearchTermForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogSearch\Test\Block\Adminhtml\Edit\SearchTermForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }
}

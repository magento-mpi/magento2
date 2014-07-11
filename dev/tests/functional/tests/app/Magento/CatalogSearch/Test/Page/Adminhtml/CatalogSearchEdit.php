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

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\CatalogSearch\Test\Block\Adminhtml\Edit\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogSearch\Test\Block\Adminhtml\Edit\Form
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

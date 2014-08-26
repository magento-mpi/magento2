<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ImportExport\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class AdminExportIndex
 */
class AdminExportIndex extends BackendPage
{
    const MCA = 'admin/export/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'filterExport' => [
            'class' => 'Magento\ImportExport\Test\Block\Adminhtml\Export\Filter',
            'locator' => '#export_filter_container',
            'strategy' => 'css selector',
        ],
        'exportForm' => [
            'class' => 'Magento\ImportExport\Test\Block\Adminhtml\Export\Edit\Form',
            'locator' => '#base_fieldset',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\ImportExport\Test\Block\Adminhtml\Export\Filter
     */
    public function getFilterExport()
    {
        return $this->getBlockInstance('filterExport');
    }

    /**
     * @return \Magento\ImportExport\Test\Block\Adminhtml\Export\Edit\Form
     */
    public function getExportForm()
    {
        return $this->getBlockInstance('exportForm');
    }
}

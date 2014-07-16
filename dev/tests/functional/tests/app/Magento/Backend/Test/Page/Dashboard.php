<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page;

use Mtf\Page\BackendPage;

/**
 * Class Dashboard
 * Dashboard (Home) page for backend
 */
class Dashboard extends BackendPage
{
    /**
     * URL part for backend authorization
     */
    const MCA = 'admin/dashboard';

    protected $_blocks = [
        'adminPanelHeader' => [
            'name' => 'adminPanelHeader',
            'class' => 'Magento\Backend\Test\Block\Page\Header',
            'locator' => '.page-header',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title',
            'strategy' => 'css selector',
        ],
        'menuBlock' => [
            'name' => 'menuBlock',
            'class' => 'Magento\Backend\Test\Block\Menu',
            'locator' => '.navigation',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * Get admin panel header block instance
     *
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getAdminPanelHeader()
    {
        return $this->getBlockInstance('adminPanelHeader');
    }

    /**
     * Get title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * Get Menu block
     *
     * @return \Magento\Backend\Test\Block\Menu
     */
    public function getMenuBlock()
    {
        return $this->getBlockInstance('menuBlock');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class Dashboard
 */
class Dashboard extends FrontendPage
{
    const MCA = 'admin/dashboard';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'adminPanelHeader' => [
            'class' => 'Magento\Backend\Test\Block\Page\Header',
            'locator' => '.page-header',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title',
            'strategy' => 'css selector',
        ],
        'menuBlock' => [
            'class' => 'Magento\Backend\Test\Block\Menu',
            'locator' => '.navigation',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getAdminPanelHeader()
    {
        return $this->getBlockInstance('adminPanelHeader');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Menu
     */
    public function getMenuBlock()
    {
        return $this->getBlockInstance('menuBlock');
    }
}

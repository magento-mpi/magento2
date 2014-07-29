<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Reward
 * Backend customer reward tab
 */
class Reward extends Tab
{
    /**
     * Reward history accordion link selector
     *
     * @var string
     */
    protected $rewardHistorySelector = '#dt-reward_points_history';

    /**
     * Reward Points History grid selector
     *
     * @var string
     */
    protected $rewardHistoryGridSelector = '#rewardPointsHistoryGrid';

    /**
     * Get customer's reward points history grid
     *
     * @return \Magento\Reward\Test\Block\Adminhtml\Edit\Tab\Reward\Grid
     */
    public function getHistoryGrid()
    {
        return $this->blockFactory->create(
            'Magento\Reward\Test\Block\Adminhtml\Edit\Tab\Reward\Grid',
            ['element' => $this->_rootElement->find($this->rewardHistoryGridSelector)]
        );
    }

    /**
     * Show Reward Points History Grid
     *
     * @return void
     */
    public function showRewardPointsHistoryGrid()
    {
        $element = $this->_rootElement;
        $grid = $this->rewardHistoryGridSelector;

        if (!$this->_rootElement->find($this->rewardHistorySelector . ".open")->isVisible()) {
            $this->_rootElement->find($this->rewardHistorySelector . " > a")->click();
            $this->_rootElement->waitUntil(
                function () use ($element, $grid) {
                    return $element->find($grid)->isVisible() ? true : null;
                }
            );
        }
    }
}

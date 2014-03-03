<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Block\Backend\Grid;

class ItemsUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if (false === $this->authorization->isAllowed('Magento_Indexer::changeMode')) {
            unset($argument['change_mode_onthefly']);
            unset($argument['change_mode_changelog']);
        }
        return $argument;
    }
}

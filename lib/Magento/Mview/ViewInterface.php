<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview;

interface ViewInterface
{
    /**
     * Fill view data from config
     *
     * @param string $viewId
     * @return \Magento\Mview\ViewInterface
     * @throws \InvalidArgumentException
     */
    public function load($viewId);

    /**
     * Create subscriptions
     *
     * @throws \Exception
     * @return \Magento\Mview\ViewInterface
     */
    public function subscribe();

    /**
     * Remove subscriptions
     *
     * @throws \Exception
     * @return \Magento\Mview\ViewInterface
     */
    public function unsubscribe();

    /**
     * @return mixed
     */
    public function update();

    /**
     * Return related state object
     *
     * @return View\StateInterface
     */
    public function getState();

    /**
     * Set view state object
     *
     * @param View\StateInterface $state
     * @return \Magento\Mview\ViewInterface
     */
    public function setState(View\StateInterface $state);

    /**
     * Return view mode
     *
     * @return string
     */
    public function getMode();

    /**
     * Return view status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Retrieve linked changelog
     *
     * @return View\ChangelogInterface
     */
    public function getChangelog();
}

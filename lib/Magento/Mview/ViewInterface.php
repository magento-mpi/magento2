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
     * @return ViewInterface
     * @throws \InvalidArgumentException
     */
    public function load($viewId);

    /**
     * Create subscriptions
     *
     * @throws \Exception
     * @return ViewInterface
     */
    public function subscribe();

    /**
     * Remove subscriptions
     *
     * @throws \Exception
     * @return ViewInterface
     */
    public function unsubscribe();

    /**
     * @return mixed
     */
    public function update();

    /**
     * Clear precessed changelog entries
     */
    public function clearChangelog();

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
     * @return ViewInterface
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

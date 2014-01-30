<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Indexer\Model;

interface IndexerInterface
{
    /**
     * Return indexer ID
     *
     * @return string
     */
    public function getId();

    /**
     * Return indexer's view ID
     *
     * @return string
     */
    public function getViewId();

    /**
     * Return indexer action class
     *
     * @return string
     */
    public function getActionClass();

    /**
     * Return indexer title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return indexer description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Fill indexer data from config
     *
     * @param string $indexerId
     * @return IndexerInterface
     * @throws \InvalidArgumentException
     */
    public function load($indexerId);

    /**
     * Return related view object
     *
     * @return \Magento\Mview\View
     */
    public function getView();

    /**
     * Return related state object
     *
     * @return Indexer\State
     */
    public function getState();

    /**
     * Set indexer state object
     *
     * @param Indexer\State $state
     * @return IndexerInterface
     */
    public function setState(Indexer\State $state);

    /**
     * Check whether indexer is run by schedule
     *
     * @return bool
     */
    public function isScheduled();

    /**
     * Turn scheduled mode on/off
     *
     * @param bool $scheduled
     */
    public function setScheduled($scheduled);

    /**
     * Check whether indexer is valid
     *
     * @return bool
     */
    public function isValid();

    /**
     * Check whether indexer is invalid
     *
     * @return bool
     */
    public function isInvalid();

    /**
     * Check whether indexer is working
     *
     * @return bool
     */
    public function isWorking();

    /**
     * Set indexer valid
     */
    public function setValid();

    /**
     * Set indexer invalid
     */
    public function setInvalid();

    /**
     * Set indexer working
     */
    public function setWorking();

    /**
     * Return indexer status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Return indexer or mview latest updated time
     *
     * @return string
     */
    public function getLatestUpdated();

    /**
     * Regenerate full index
     *
     * @throws \Exception
     */
    public function reindexAll();

    /**
     * Regenerate one row in index by ID
     *
     * @param int $id
     */
    public function reindexRow($id);

    /**
     * Regenerate rows in index by ID list
     *
     * @param int[] $ids
     */
    public function reindexList($ids);
}

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
     * Fill indexer data from config
     *
     * @param string $indexerId
     * @return \Magento\Indexer\Model\IndexerInterface
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
     * @return Indexer
     */
    public function setState(Indexer\State $state);

    /**
     * Return indexer mode
     *
     * @return string
     */
    public function getMode();

    /**
     * Return indexer status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set indexer status
     *
     * Set value to status column of indexer_state table.
     *
     * @param string $value
     * @return Indexer
     */
    public function setStatus($value);

    /**
     * Return indexer updated time
     *
     * @return string
     */
    public function getUpdated();

    /**
     * Turn changelog mode of
     *
     * @return string
     */
    public function turnViewOff();

    /**
     * Turn changelog mode on
     *
     * @return string
     */
    public function turnViewOn();

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

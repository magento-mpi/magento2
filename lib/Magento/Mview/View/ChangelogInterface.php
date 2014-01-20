<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View;

interface ChangelogInterface
{
    /**
     * Create changelog table
     *
     * @return boolean
     */
    public function create();

    /**
     * Drop changelog table
     *
     * @return boolean
     */
    public function drop();

    /**
     * Clear changelog by version_id
     *
     * @param $versionId
     * @return boolean
     */
    public function clear($versionId);

    /**
     * Retrieve entity ids by range [$fromVersionId..$toVersionId]
     *
     * @param integer $fromVersionId
     * @param integer $toVersionId
     * @return int[]
     */
    public function getList($fromVersionId, $toVersionId);

    /**
     * Get maximum version_id from changelog
     *
     * @return int
     */
    public function getVersion();

    /**
     * Get changlog name
     *
     * @return string
     */
    public function getName();

    /**
     * Get changlog entity column name
     *
     * @return string
     */
    public function getColumnName();
}

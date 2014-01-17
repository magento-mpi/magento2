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
     * Create changelog
     *
     * @return boolean
     */
    public function create();

    /**
     * Remove changelog
     *
     * @return boolean
     */
    public function remove();

    /**
     * Clear changelog by version_id
     *
     * @param $versionId
     * @return boolean
     */
    public function clear($versionId);

    /**
     * Retrieve entity ids by version_id
     *
     * @param $versionId
     * @return integer[]
     */
    public function getList($versionId);

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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Api\Data;

/**
 * Interface BlockInterface
 */
interface BlockInterface
{
    const ID = 'block_id';

    const IDENTIFIER = 'identifier';

    const TITLE = 'title';

    const CONTENT = 'content';

    const CREATION_TIME = 'creation_time';

    const UPDATE_TIME ='update_time';

    const IS_ACTIVE ='is_active';

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId();

    /**
     * Retrieve block identifier
     *
     * @return int
     */
    public function getIdentifier();

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Retrieve block content
     *
     * @return string
     */
    public function getContent();

    /**
     * Retrieve block creation time
     *
     * @return string
     */
    public function getCreationTime();

    /**
     * Retrieve block update time
     *
     * @return string
     */
    public function getUpdateTime();

    /**
     * Retrieve block status
     *
     * @return int
     */
    public function getIsActive();
}

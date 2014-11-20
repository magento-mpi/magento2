<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Api\Data;

/**
 * Interface Page
 * @package Magento\Cms\Api\Data
 */
interface PageInterface
{
    const PAGE_ID = 'page_id';

    const IDENTIFIER = 'identifier';

    const TITLE = 'title';

    /**
     * Retrieve page identifier
     *
     * @return int
     */
    public function getPageId();

    /**
     * Retrieve page identifier
     *
     * @return int
     */
    public function getIdentifier();

    /**
     * Retrieve page title
     *
     * @return string
     */
    public function getTitle();
}

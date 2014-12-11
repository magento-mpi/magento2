<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Cms\Api\Data;

/**
 * Interface Page
 * @data-api
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
    public function getId();

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

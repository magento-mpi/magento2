<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

interface SearchDataInterface
{
    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSearchData();

    /**
     * @return bool
     */
    public function isCountResultsEnabled();

    /**
     * @param string $queryText
     * @return string
     */
    public function getLink($queryText);

    /**
     * @return string
     */
    public function getTitle();
}

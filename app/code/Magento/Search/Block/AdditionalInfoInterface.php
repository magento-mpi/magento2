<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

interface AdditionalInfoInterface
{
    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getAdditionalInfo();

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

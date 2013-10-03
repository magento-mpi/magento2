<?php
/**
 * Interface for keeping track of the current block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

interface BlockTrackerInterface
{
    /**
     * Get the current block
     * @return \Magento\Core\Block\Template
     */
    public function getCurrentBlock();
}

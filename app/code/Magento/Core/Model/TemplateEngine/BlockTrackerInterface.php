<?php
/**
 * Interface for keeping track of the current block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_TemplateEngine_BlockTrackerInterface
{
    /**
     * Get the current block
     * @return Magento_Core_Block_Template
     */
    public function getCurrentBlock();
}
<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to highliht dynamic blocks
 */
class Enterprise_PageCache_Block_Debug extends Mage_Core_Block_Template
{
    /**
     * Set default debug template
     */
    public function __construct()
    {
        $this->setTemplate('blockdebug.phtml');
    }
}

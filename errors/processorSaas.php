<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processor.php';

/**
 * Saas error processor
 */
class Error_ProcessorSaas extends Error_Processor
{
    /**
     * Rewrite skin
     */
    public function __construct()
    {
        parent::__construct();
        $this->_setSkin('go');
    }

    /**
     * Process no cache error
     */
    public function processNoCache()
    {
        $this->pageTitle = 'Error : cached config data is unavailable';
        $this->_renderPage('nocache.phtml');
    }
}

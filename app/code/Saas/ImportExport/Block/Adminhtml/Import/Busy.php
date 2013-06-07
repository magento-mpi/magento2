<?php
/**
 * Import Busy Block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_Busy extends Mage_Backend_Block_Template
{
    /**
     * Block status message
     *
     * @var string
     */
    protected $_statusMessage = '';

    /**
     * Set status message
     *
     * @param string $message
     */
    public function setStatusMessage($message)
    {
        $this->_statusMessage = (string)$message;
    }

    /**
     * Get status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }
}

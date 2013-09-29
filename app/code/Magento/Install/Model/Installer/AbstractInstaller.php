<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract installer model
 *
 * @category   Magento
 * @package    Magento_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Model\Installer;

class AbstractInstaller
{
    /**
     * Installer singleton
     *
     * @var \Magento\Install\Model\Installer
     */
    protected $_installer;

    /**
     * Get installer singleton
     *
     * @return \Magento\Install\Model\Installer
     */
    protected function _getInstaller()
    {
        if (is_null($this->_installer)) {
            $this->_installer = \Mage::getSingleton('Magento\Install\Model\Installer');
        }
        return $this->_installer;
    }

    /**
     * Validate session storage value (files or db)
     * If empty, will return 'files'
     *
     * @param string $value
     * @return string
     * @throws \Exception
     */
    protected function _checkSessionSave($value)
    {
        if (empty($value)) {
            return 'files';
        }
        if (!in_array($value, array('files', 'db'), true)) {
            throw new \Exception('session_save value must be "files" or "db".');
        }
        return $value;
    }

    /**
     * Validate backend area frontname value.
     * If empty, "backend" will be returned
     *
     * @param string $value
     * @return string
     * @throws \Exception
     */
    protected function _checkBackendFrontname($value)
    {
        if (empty($value)) {
            return 'backend';
        }
        if (!preg_match('/^[a-z]+[a-z0-9_]+$/', $value)) {
            throw new \Exception('backend_frontname value must contain only letters (a-z), numbers (0-9)'
                . ' or underscore(_), first character should be a letter.');
        }
        return $value;
    }
}

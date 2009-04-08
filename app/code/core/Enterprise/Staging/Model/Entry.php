<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entry points model
 *
 */
class Enterprise_Staging_Model_Entry
{
    /**
     * @var Mage_Core_Model_Website
     */
    protected $_website;
    /**
     * @var string
     */
    protected $_baseFolderName;

    /**
     * Get entry point name from system config
     */
    public function __construct()
    {
        $this->_baseFolderName = Mage::getStoreConfig('general/content_staging/entry_points_folder_name');
    }

    /**
     * Check whether entry points should be created automatically
     *
     * @return bool
     */
    public function isAutomatic()
    {
        return (bool)(int)Mage::getStoreConfig('general/content_staging/create_entry_point');
    }

    /**
     * Get base folder for entry points
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getBaseFolder()
    {
        if (empty($this->_baseFolderName)) {
            Mage::throwException(Mage::helper('enterprise_staging')->__('There is wrong value in configuration for entry points folder name.'));
        }
        return BP . DS . $this->_baseFolderName;
    }

    /**
     * Website setter
     *
     * @param Mage_Core_Model_Website $website
     * @return Enterprise_Staging_Model_Entry
     */
    public function setWebsite($website)
    {
        $this->_website = $website;
        return $this;
    }

    /**
     * Get filename of entry point to current website
     *
     * @return string
     */
    public function getFilename()
    {
        $this->_ensureWebsite();
        return $this->getBaseFolder() . DS . $this->_website->getCode() . DS . 'index.php';
    }

    /**
     * Check whether entry point can be created
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function canEntryPointBeCreated()
    {
        $folder = $this->getBaseFolder();
        if ((!is_dir($folder)) || (!is_writeable($folder))) {
            Mage::throwException(Mage::helper('enterprise_staging')->__('To create entry points, the folder %s must be writeable.', $folder));
        }
        if ($this->_website && $this->_website->getCode()) {
            if (file_exists($this->getFilename())) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate a base URL for a website like if it is staging
     *
     * @param bool $secure
     * @return string
     */
    public function getBaseUrl($secure = false)
    {
        $this->_ensureWebsite();
        $this->getBaseFolder();
        return Mage::getStoreConfig('web/' . ($secure ? '' : 'un') . 'secure/base_url', 0) . $this->_baseFolderName . '/' . $this->_website->getCode() . '/';
    }

    /**
     * Create entry point if possible
     *
     * @return Enterprise_Staging_Model_Entry
     */
    public function save()
    {
        $this->_ensureWebsite();
        if ($this->canEntryPointBeCreated()) {
            $sample = file_get_contents(BP . DS . 'index.php.sample');
            $outputFile = $this->getFilename();
            if (!is_dir(dirname($outputFile))) {
                mkdir(dirname($outputFile));
            }
            file_put_contents($outputFile, str_replace(
                array('\'app/Mage.php\'', 'Mage::run(\'default\');'),
                array('\'../../app/Mage.php\'', "Mage::run('{$this->_website->getCode()}', 'website');"),
                $sample
            ));
        }
        return $this;
    }

    /**
     * Make sure website is set
     *
     * @throws Mage_Core_Exception
     */
    protected function _ensureWebsite()
    {
        if ((!$this->_website) || (!$this->_website->getCode())) {
            Mage::throwException(Mage::helper('enterprise_staging')->__('Website code is not defined.'));
        }
    }
}

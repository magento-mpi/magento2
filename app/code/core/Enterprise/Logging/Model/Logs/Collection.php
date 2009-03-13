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
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Logging_Model_Logs_Collection extends Varien_Data_Collection
{
    /**
     * Is loaded data flag
     * @var boolean
     */
    protected $_isLoaded = false;

    public static $allowDirs     = '/^[a-z0-9\.\-]+$/i';
    public static $allowFiles    = '/^[a-z0-9\.\-\_]+\.(xml|ser|csv)$/i';
    public static $disallowFiles = '/^package\.xml$/i';


    /**
     * Get all packages identifiers
     *
     * @return array
     */
    protected function _fetchPackages()
    {
        $baseDir = Mage::getModel('enterprise_logging/logs')->getBasePath();
        $files = array();
        $this->_collectRecursive($baseDir,  $files);
        $result = array();
        foreach ($files as $file) {
            $file = preg_replace(array('/^' . preg_quote($baseDir . DS, '/') . '/', '/\.(xml|ser)$/'), '', $file);
            $result[] = array(
                'filename'    => $file,
                'filename_id' => $file
            );
        }
        return $result;
    }

    /**
     * Get package files from directory recursively
     *
     * @param string $dir
     * @param array &$result
     * @param bool $dirsFirst
     */
    protected function _collectRecursive($dir, &$result, $dirsFirst = true)
    {
        $_result = glob($dir . DS . '*');

        if (!is_array($_result)) {
            return;
        }

        if (!$dirsFirst) {
            // collect all the stuff recursively
            foreach ($_result as $item) {
                if (is_dir($item) && preg_match(self::$allowDirs, basename($item))) {
                    $this->_collectRecursive($item, $result, $dirsFirst);
                }
                elseif (is_file($item)
                    && preg_match(self::$allowFiles, basename($item))
                    && !preg_match(self::$disallowFiles, basename($item))) {
                        $result[] = $item;
                }
            }
        }
        else {
            // collect directories first
            $dirs  = array();
            $files = array();
            foreach ($_result as $item) {
                if (is_dir($item) && preg_match(self::$allowDirs, basename($item))) {
                    $dirs[] = $item;
                }
                elseif (is_file($item)
                    && preg_match(self::$allowFiles, basename($item))
                    && !preg_match(self::$disallowFiles, basename($item))) {
                        $files[] = $item;
                }
            }
            // search directories recursively
            foreach ($dirs as $item) {
                $this->_collectRecursive($item, $result, $dirsFirst);
            }
            // add files
            foreach ($files as $item) {
                $result[] = $item;
            }
        }
    }

    /** ----------------------------- **/
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // fetch packages specific to source
        $packages = $this->_fetchPackages();

        // apply filters
        if (!empty($this->_filters)) {
            foreach ($packages as $i=>$pkg) {
                if (!$this->validateRow($pkg)) {
                    unset($packages[$i]);
                }
            }
        }

        // find totals
        $this->_totalRecords = sizeof($packages);
        $this->_setIsLoaded();

        // sort packages
        if (!empty($this->_orders)) {
            usort($packages, array($this, 'sortPackages'));
        }

        // pagination and add to collection
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;

        $cnt = 0;
        foreach ($packages as $pkg) {
            $cnt++;
            if ($cnt<$from || $cnt>$to) {
                continue;
            }
            $item = new $this->_itemObjectClass();
            $item->addData($pkg);
            $this->addItem($item);
        }

        return $this;
    }

    public function setOrder($field, $dir='desc')
    {
        $this->_orders[] = array('field'=>$field, 'dir'=>$dir);
        return $this;
    }

    public function sortPackages($a, $b)
    {
        $field = $this->_orders[0]['field'];
        $dir = $this->_orders[0]['dir'];

        $cmp = $a[$field] > $b[$field] ? 1 : ($a[$field] < $b[$field] ? -1 : 0);

        return ('asc'===$dir) ? $cmp : -$cmp;
    }

    public function addFieldToFilter($field, $condition)
    {
        $this->_filters[$field] = $condition;
        return $this;
    }

    public function validateRow($row)
    {
        return true;
    }

    public function getAllIds()
    {
        $this->load();

        $ids = array();
        foreach ($this->getIterator() as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }
















}
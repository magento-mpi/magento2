<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
            $names = explode('/', $file);
            $date = sprintf("%s-%s-%s", $names[0], $names[1], substr($names[2], 6, 2));

            $result[] = array(
                'filename'    => $file,
                'filename_id' => $file,
                'date'        => $date,
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

    /** 
     * Load files from disk
     */
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

    /**
     * Process grid 'order' action
     */

    public function setOrder($field, $dir='desc')
    {
        $this->_orders[] = array('field'=>$field, 'dir'=>$dir);
        return $this;
    }

    /**
     * Sorting files
     */
    public function sortPackages($a, $b)
    {
        $field = $this->_orders[0]['field'];
        $dir = $this->_orders[0]['dir'];

        $cmp = $a[$field] > $b[$field] ? 1 : ($a[$field] < $b[$field] ? -1 : 0);

        return ('asc'===$dir) ? $cmp : -$cmp;
    }

    /**
     * add filter
     */
    public function addFieldToFilter($field, $condition)
    {
        $this->_filters[$field] = $condition;
        return $this;
    }

    /**
     * Process filter
     */
    public function validateRow($row)
    {
        if (empty($this->_filters)) {
            return true;
        }
        foreach ($this->_filters as $field=>$filter) {
            if (!isset($row[$field])) {
                return false;
            }
            if (isset($filter['eq'])) {
                if ($filter['eq']!=$row[$field]) {
                    return false;
                }
            }
            if (isset($filter['like'])) {
                $query = preg_replace('#(^%|%$)#', '', $filter['like']);
                if (strpos(strtolower($row[$field]), strtolower($query))===false) {
                    return false;
                }
            }
            if ($field == 'date') {
                if (isset($filter['from']) && ($from = $filter['from'])) {
                    if ($from->getTimestamp() > strtotime($row['date']))
                        return false;
                }
                if (isset($filter['to']) && ($to = $filter['to'])) {
                    if ($to->getTimestamp() < strtotime($row['date']))
                        return false;
                }
            }
            if ('version'===$field) {
                if (isset($filter['from'])) {
                    if (!version_compare($filter['from'], $row[$field], '<=')) {
                        return false;
                    }
                }
                if (isset($filter['to'])) {
                    if (!version_compare($filter['to'], $row[$field], '>=')) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Return files identifiers
     */
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
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
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert csv parser
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Dataflow_Model_Convert_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    protected $_fields;

    public function parse()
    {

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

//        $fp = tmpfile();
//        fputs($fp, $this->getData());
//        fseek($fp, 0);
//
//        $data = array();
//        for ($i=0; $line = fgetcsv($fp, 4096, $fDel, $fEnc); $i++) {
//            $data[] = $this->parseRow($i, $line);
//        }
//        fclose($fp);

        if (Mage::app()->getRequest()->getParam('files')) {
            $path = Mage::app()->getConfig()->getTempVarDir().'/import/';
            $file = $path.Mage::app()->getRequest()->getParam('files');
            if (file_exists($file)) {
                $data = file_get_contents($file);
            }
        } else {
            $data = $this->getData();
        }
        if ($this->getVar('adapter') && $this->getVar('method')) {
            $adapter = Mage::getModel($this->getVar('adapter'));
        }
        if (isset($data) && isset($adapter)) foreach (explode("\n", $data) as $i=>$line) {
            $line = trim($line);
            $row = $this->parseRow(compact('i', 'line'));

            if ($row) {
                //$this->getAction()->runActions(compact('i', 'row'));
                $loadMethod = $this->getVar('method');
                $adapter->$loadMethod(compact('i', 'row'));
            }
        }
        #$this->setData($data);
        return $this;
    }

    public function parseRow($args)
    {
        $i = $args['i'];
        $line = $args['line'];
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        if (is_string($line)) {
            $line = mageParseCsv($line, $fDel, $fEnc);
        }

        if (sizeof($line) == 1) return false;

        if (0==$i) {
            if ($this->getVar('fieldnames')) {
                $this->_fields = $line;
                return;
            } else {
                foreach ($line as $j=>$f) {
                    $this->_fields[$j] = 'column'.($j+1);
                }
            }
        }
        $resultRow = array();

        foreach ($this->_fields as $j=>$f) {
            $resultRow[$f] = isset($line[$j]) ? $line[$j] : '';
        }
        return $resultRow;
    }

    public function unparse()
    {
        $csv = '';

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');
        $lDel = "\r\n";

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        $data = $this->getData();
        $this->_fields = $this->getGridFields($data);
        $lines = array();

        if ($this->getVar('fieldnames')) {
            $line = array();
            foreach ($this->_fields as $f) {
                $v = isset($f) ? str_replace('\\', $fEsc.'\\', $f) : '';
                $line[] = str_replace('"', '\"', $v);
                //$line[] = $fEnc.str_replace(array('"', '\\'), array('\"', $fEsc.'\\'), $f).$fEnc;
            }
            $lines[] = join($fDel, $line);
        }
        foreach ($data as $i=>$row) {
//            $lines[] = $this->unparseRow(compact($i, $row));
            $lines[] = $this->unparseRow(compact('i', 'row'));
        }
        $result = join($lDel, $lines);
        $this->setData($result);

        return $this;
    }

    public function unparseRow($args)
    {
        $i = $args['i'];
        $row = $args['row'];

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');
        $lDel = "\r\n";

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        $line = array();
        foreach ($this->_fields as $f) {
            $v = isset($row[$f]) ? str_replace(array('"', '\\'), array($fEsc.'"', $fEsc.'\\'), $row[$f]) : '';
            $line[] = $fEnc.$v.$fEnc;
        }

        return join($fDel, $line);
    }

}

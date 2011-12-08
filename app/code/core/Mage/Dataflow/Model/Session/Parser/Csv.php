<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert csv parser
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Session_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Abstract
{

    public function parse()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

        $fp = tmpfile();
        fputs($fp, $this->getData());
        fseek($fp, 0);

        $data = array();
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getModel('Mage_Dataflow_Model_Import');
        $map = new Varien_Convert_Mapper_Column();
        for ($i=0; $line = fgetcsv($fp, 4096, $fDel, $fEnc); $i++) {
            if (0==$i) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach ($line as $j=>$f) {
                        $fields[$j] = 'column'.($j+1);
                    }
                }
            }
            $row = array();
            foreach ($fields as $j=>$f) {
                $row[$f] = $line[$j];
            }
            /*
            if ($i <= 100)
            {
                $data[] = $row;
            }
            */
            //$map = new Varien_Convert_Mapper_Column();
            $map->setData(array($row));
            $map->map();
            $row = $map->getData();
            //$import = Mage::getModel('Mage_Dataflow_Model_Import');
            $import->setImportId(0);
            $import->setSessionId($sessionId);
            $import->setSerialNumber($i);
            $import->setValue(serialize($row[0]));
            $import->save();
            //unset($import);
        }
        fclose($fp);
        unset($sessionId);
        //$this->setData($data);
        return $this;
    } // end

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
        $fields = $this->getGridFields($data);
        $lines = array();

        if ($this->getVar('fieldnames')) {
            $line = array();
            foreach ($fields as $f) {
                $line[] = $fEnc.str_replace(array('"', '\\'), array($fEsc.'"', $fEsc.'\\'), $f).$fEnc;
            }
            $lines[] = join($fDel, $line);
        }
        foreach ($data as $i=>$row) {
            $line = array();
            foreach ($fields as $f) {
                /*
                if (isset($row[$f]) && (preg_match('\"', $row[$f]) || preg_match('\\', $row[$f]))) {
                    $tmp = str_replace('\\', '\\\\',$row[$f]);
                    echo str_replace('"', '\"',$tmp).'<br>';
                }
                */
                $v = isset($row[$f]) ? str_replace(array('"', '\\'), array($fEsc.'"', $fEsc.'\\'), $row[$f]) : '';

                $line[] = $fEnc.$v.$fEnc;
            }
            $lines[] = join($fDel, $line);
        }
        $result = join($lDel, $lines);
        $this->setData($result);

        return $this;
    }

}

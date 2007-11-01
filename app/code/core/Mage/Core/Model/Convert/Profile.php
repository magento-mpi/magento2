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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Model_Convert_Profile extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/convert_profile');
    }

    protected function _afterLoad()
    {
        if (is_string($this->getData('gui_data'))) {
            $guiData = unserialize($this->getData('gui_data'));
        } else {
            $guiData = '';
        }
        $this->setData('gui_data', $guiData);

        parent::_afterLoad();
    }

    protected function _beforeSave()
    {
        $guiData = $this->getGuiData();
        if (is_array($guiData)) {
            if (isset($guiData['map']) && is_array($guiData['map'])) {
                foreach ($guiData['map'] as $side=>$fields) {
                    array_shift($guiData['map'][$side]['db']);
                    array_shift($guiData['map'][$side]['file']);
                }
            }
            $this->_parseGuiData();
            $this->setGuiData(serialize($guiData));
        }

        parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if (is_string($this->getData('gui_data'))) {
            $this->setData('gui_data', unserialize($this->getData('gui_data')));
        }
        parent::_afterSave();
    }

    public function run()
    {
        $xml = '<convert version="1.0"><profile name="default">'.$this->getActionsXml().'</profile></convert>';
        $profile = Mage::getModel('core/convert')->importXml($xml)->getProfile('default');
        try {
            $profile->run();
        } catch (Exception $e) {

        }
        $this->setExceptions($profile->getExceptions());
        return $this;
    }

    public function _parseGuiData()
    {
        $nl = "\r\n";
        $import = $this->getDirection()==='import';
        $p = $this->getGuiData();
echo "<pre>".print_r($p,1)."</pre>";

        switch ($p['file']['method']) {
            case 'io':
                $fileXml = '<action type="core/convert_adapter_io" method="'.($import?'load':'save').'">'.$nl;
                $fileXml .= '    <var name="type">'.$p['file']['type'].'</var>'.$nl;
                $fileXml .= '    <var name="path">'.$p['file']['path'].'</var>'.$nl;
                $fileXml .= '    <var name="filename"><![CDATA['.$p['file']['filename'].']]></var>'.$nl;
                if ($p['file']['type']==='ftp') {
                    $hostArr = explode(':', $p['file']['host']);
                    $fileXml .= '    <var name="host"><![CDATA['.$hostArr[0].']]></var>'.$nl;
                    if (isset($hostArr[1])) {
                        $fileXml .= '    <var name="port"><![CDATA['.$hostArr[1].']]></var>'.$nl;
                    }
                    if (!empty($p['file']['passive'])) {
                        $fileXml .= '    <var name="passive">true</var>';
                    }
                    if (!empty($p['file']['user'])) {
                        $fileXml .= '    <var name="user"><![CDATA['.$p['file']['user'].']]></var>'.$nl;
                    }
                    if (!empty($p['file']['password'])) {
                        $fileXml .= '    <var name="password"><![CDATA['.$p['file']['password'].']]></var>'.$nl;
                    }
                }
                break;

            case 'http':
                $fileXml = '<action type="varien/convert_adapter_http" method="'.($import?'load':'save').'">'.$nl;
                break;
        }
        $fileXml .= '</action>'.$nl.$nl;

        switch ($p['parse']['type']) {
            case 'excel_xml':
                $parseFileXml = '<action type="varien/convert_parser_xml_excel" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="single_sheet"><![CDATA['.($p['parse']['single_sheet']!==''?$p['parse']['single_sheet']:'_').']]></var>'.$nl;
                break;

            case 'csv':
                $parseFileXml = '<action type="core/convert_parser_csv" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="delimiter"><![CDATA['.$p['parse']['delimiter'].']]></var>'.$nl;
                $parseFileXml .= '    <var name="enclose"><![CDATA['.$p['parse']['enclose'].']]></var>'.$nl;
                break;
        }
        $parseFileXml .= '    <var name="fieldnames">'.$p['parse']['fieldnames'].'</var>'.$nl;
        $parseFileXml .= '</action>'.$nl.$nl;

        $mapXml = '';
        if (sizeof($p['map']['db'])>0) {
            $mapXml .= '<action type="varien/convert_mapper_column" method="map">'.$nl;
            $from = $p['map'][$import?'file':'db'];
            $to = $p['map'][$import?'db':'file'];
            foreach ($from as $i=>$f) {
                if ($i===0) {
                    continue;
                }
                $mapXml .= '    <var name="'.$f.'"><![CDATA['.$to[$i].']]></var>'.$nl;
            }
            $mapXml .= '</action>'.$nl.$nl;
        }

        $parseDataXml = '<action type="catalog/convert_parser_product" method="'.($import?'parse':'unparse').'">'.$nl;
        if ($p['eav']['target_store']==='specific') {
            $parseDataXml .= '    <var name="store"><![CDATA['.$p['eav']['store'].']]></var>'.$nl;
        }
        $parseDataXml .= '</action>'.$nl.$nl;

        $eavXml = '<action type="catalog/convert_adapter_product" method="'.($import?'save':'load').'">'.$nl;
        if ($p['eav']['target_store']==='specific') {
            $eavXml .= '    <var name="store"><![CDATA['.$p['eav']['store'].']]></var>'.$nl;
        }
        $eavXml .= '</action>'.$nl.$nl;

        if ($import) {
            $xml = $fileXml.$parseFileXml.$mapXml.$parseDataXml.$eavXml;
        } else {
            $xml = $eavXml.$parseDataXml.$mapXml.$parseFileXml.$fileXml;
        }

        $this->setActionsXml($xml);

        return $this;
    }
}
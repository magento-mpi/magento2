<?php

class Mage_Adminhtml_Model_System_Convert_Generate_Products
{
    public function generateXml($p)
    {
        $nl = "\r\n";
        $import = $p['profile']['direction']==='import';

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

        return $xml;
    }
}
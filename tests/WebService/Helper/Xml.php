<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Xml
 *
 * @author Vladimir
 */
class WebService_Helper_Xml
{
    public static function simpleXmlToArray($xml)
    {
        $result = array();

        foreach (get_object_vars($xml->children()) as $key => $node)
        {
            if (is_object($node)){
                $result[$key] = WebService_Helper_Xml::simpleXmlToArray($node);
            } else {
                $result[$key] = (string)$node;
            }
        }
        return $result;
    }

    public static function applyIgnore(array $data, $ignore, $iter = 0)
    {
        $result = array();
        foreach ($data as $key => $entriy) {
            if (is_array($entriy)){
                if ($iter == 0){
                    $key = null;
                }
                $result[$key] = WebService_Helper_Xml::applyIgnore($entriy, $ignore, $iter + 1);
            }
            else {

                if (!array_key_exists($key, $ignore)){
                    $result[$key] = $entriy;
                }
            }
        }
        return $result;
    }

    public static function getArrayFromObject($object, $iter = 0)
    {
        $result = array();
        foreach ($object as $key => $entriy) {
            if (is_object($entriy)){
                if ($iter == 0){
                    $key = null;
                }
                $result[$key] = WebService_Helper_Xml::getArrayFromObject($entriy, $iter + 1);
            }
            else {
                    $result[$key] = $entriy;
            }
        }
        return $result;
    }

    public static function getFilter($pathToXml)
    {
        $mageFilters = new StdClass();
        $complexFiltersCollection = Array();
        $xml = simplexml_load_file($pathToXml);
        foreach($xml->filters->children() as $filerKey => $filterValue){
            $attr = $filterValue->attributes();
            $sign = $attr['sign'];
            $complexFiltersItem = new StdClass();
            $complexFiltersItem->key = $filerKey;
            $filterEntity = new StdClass();
            $filterEntity->key = $sign;
            $filterEntity->value = $filterValue;
            $complexFiltersItem->value = $filterEntity;
            $complexFiltersCollection[] = $complexFiltersItem;
            $mageFilters->complex_filter = $complexFiltersCollection;
        }
        return $mageFilters;
    }

    public static function getModuleList($pathToFile)
    {
        $xml = simplexml_load_file($pathToFile);
        foreach($xml->modules->children() as $module) {
            if( (string)$module->is_active == 'true') {
                $moduleList[] = $module->getName();
            }
        }
        return $moduleList;
    }

    public static function getValueByPath($pathToFile, $xPath)
    {
        $xml = simplexml_load_file($pathToFile);
        $xmlObj = $xml->xpath($xPath);
        return (string)$xmlObj[0];
    }
}
?>

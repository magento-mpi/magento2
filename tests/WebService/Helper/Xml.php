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
    /**
     * @param SimpleXMLObject $xml
     * @param String $keyTrimmer
     * @return array
     *
     * In XML notation we can't have nodes with digital names in other words fallowing XML will be not valid:
     * &lt;24&gt;
     *      Defoult category
     * &lt;/24&gt;
     *
     * But this one will not cause any problems:
     * &lt;qwe_24&gt;
     *      Defoult category
     * &lt;/qwe_24&gt;
     *
     * So when we want to obtain an array with key 24 we will pass the correct XML from above and $keyTrimmer = 'qwe_';
     * As a result we will obtain an array with digital key node.
     *
     * In the other case just don't pass the $keyTrimmer.
     */
    public static function simpleXmlToArray($xml, $keyTrimmer = null)
    {
        $result = array();

        $isTrimmed = false;
        if (!is_null($keyTrimmer)){
            $isTrimmed = true;
        }

        if(is_object($xml)){
            foreach (get_object_vars($xml->children()) as $key => $node)
            {
                $arrKey = $key;
                if ($isTrimmed){
                    $arrKey = str_replace($keyTrimmer, '', $key);//, &$isTrimmed);
                }
                if (is_numeric($arrKey)){
                    $arrKey = 'Obj' . $arrKey;
                }
                if (is_object($node)){
                    $result[$arrKey] = WebService_Helper_Xml::simpleXmlToArray($node, $keyTrimmer);
                } elseif(is_array($node)){
                    $result[$arrKey] = array();
                    foreach($node as $node_key => $node_value){
                        $result[$arrKey][] = WebService_Helper_Xml::simpleXmlToArray($node_value, $keyTrimmer);
                    }
                } else {
                    $result[$arrKey] = (string)$node;
                }
            }
        } else {
            $result = (string) $xml;
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

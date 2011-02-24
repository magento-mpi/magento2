<?php

class WebService_Helper_Data
{
    protected static $_data = array();

    public static function set($key, $value)
    {
        self::$_data[$key] = $value;
    }

    public static function get($key)
    {    
        return self::$_data[$key];
    }

    public static function transformToClass($path)
    {
        return str_replace('/', '_', $path);
    }

    private static function _push(&$obj, $key, $value){
        if(is_numeric($key)){
            $key = 'Obj' . $key;
        }
        $obj->$key = $value;
    }

    /**
     * @param Array $arr
     * @return object
     */
    public static function arrayToObject($arr) {
        $obj = new stdClass();
        if(is_array($arr)){
            foreach($arr as $key => $value){
                if(is_array($value)){
                    self::_push($obj, $key, self::arrayToObject($value));
                } else {
                    self::_push($obj, $key, $value);
                }
            }
        }
        return $obj;
    }

    /**
     * @param object $obj
     * @return Array
     */
    public static function objectToArray($obj)
    {
        if (is_object($obj) && null !== ($_data = get_object_vars($obj))) {
            foreach ($_data as $key => $value) {
                if(is_object($value)){
                    $_data[$key] = self::objectToArray($value);
                }
            }
            return $_data;
        } elseif ( is_array($obj) ){
            return $obj;
        }
        return array();
    }
    
    /**
     * Will return an array, only with keys, specified at "$arrayKeys".
     * @param Array $arr
     * @param Array $arrayKeys
     * @return Array
     */
    public static function filterArray(Array $arr, $arrayKeys) {
        $res = array();
        foreach ($arr as $arrItem) {
            $tmpArr = array();
            foreach ($arrayKeys as $arrayKeyItem) {
                if(isset($arrItem[$arrayKeyItem])){
                    $tmpArr[$arrayKeyItem] = $arrItem[$arrayKeyItem];
                }
            }
            $res[] = $tmpArr;
        }
        return $res;
    }
}
?>

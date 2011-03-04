<?php
class customParserTag extends parserTag
{
    var $keyword = '_customTag';
    public $phpDocOptions = array();

    public function getSettings($namespace=false, $key = false)
    {
        global $_phpDocumentor_setting;

        if (empty($namespace) || empty($key)) return false;
        $value = false;

        $ini_array = '_phpDocumentor_' . $namespace;
        global $$ini_array;

        if (isset($$ini_array[$key])) {
            $value = $$ini_array[$key];
        }

        if (isset($_phpDocumentor_setting[$key])) {
            $value = $_phpDocumentor_setting[$key];
        }

        return $value;
    }




}
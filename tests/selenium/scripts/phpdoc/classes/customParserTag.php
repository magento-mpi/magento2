<?php
class customParserTag extends parserTag
{
    var $keyword = '_customTag';
    public $phpDocOptions = array();

    public function getSettings($key = false)
    {
        global $_phpDocumentor_setting;

        if (empty($key)) return false;
        $value = false;

        $ini_array = '_phpDocumentor_tag_' . $this->keyword;
        global ${$ini_array};

        if (isset($_phpDocumentor_setting[$key])) {
            $value = $_phpDocumentor_setting[$key];
        } else if (isset(${$ini_array}[$key])) {
            $value = ${$ini_array}[$key];
        }

        return $value;
    }


}
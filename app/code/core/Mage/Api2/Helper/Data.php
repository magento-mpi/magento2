<?php


class Mage_Api2_Helper_Data
{
    public static function getTypeMapping()
    {
        $types = array(
            'text'                  => 'query',
            'text/plain'            => 'query',
            'text/html'             => 'query',
            'json'                  => 'json',
            'application/json'      => 'json',
            'xml'                   => 'xml',
            'application/xml'       => 'xml',
            'application/xhtml+xml' => 'xml',
            'text/xml'              => 'xml',

        );

        return $types;
    }

    public static function simpleXmlToArray($xml, $keyTrimmer = null)
    {
        $result = array();

        $isTrimmed = false;
        if (null !== $keyTrimmer){
            $isTrimmed = true;
        }

        if (is_object($xml)){
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
                    $result[$arrKey] = self::simpleXmlToArray($node, $keyTrimmer);
                } elseif(is_array($node)){
                    $result[$arrKey] = array();
                    foreach($node as $node_key => $node_value){
                        $result[$arrKey][] = self::simpleXmlToArray($node_value, $keyTrimmer);
                    }
                } else {
                    $result[$arrKey] = (string) $node;
                }
            }
        } else {
            $result = (string) $xml;
        }
        return $result;
    }
}

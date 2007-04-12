<?php

class Varien_Crypt
{
    static public function factory($method='mcrypt')
    {
        $uc = str_replace(' ','_',ucwords(str_replace('_',' ',$method)));
        $className = 'Varien_Crypt_'.$uc;
        return new $className;
    }
}
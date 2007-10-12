<?php

define("EXTENSION",'csv');
$CONFIG['allow_extensions'] = array('php','xml','phtml','csv');

$CONFIG['translates'] = array(

    'Mage_Adminhtml' => array(
        'app/code/core/Mage/Admin/',
        'app/code/core/Mage/Adminhtml/',
        'app/design/adminhtml/default/default/template/',
    ),

    'Mage_Catalog' => array(
        'app/code/core/Mage/Catalog/',
        'app/design/frontend/default/default/template/catalog/',
        '',
    ),

);

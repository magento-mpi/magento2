<?php
#include_once "Ecom/Core/Controller/Zend/Admin/Action.php";

class ModulesController extends Ecom_Core_Controller_Zend_Admin_Action
{
     function listAction() {
        $blocks = "({'totalRecords':'8',
                    'modules':[{'name':'Module1','module_id':'1','descr':'The layout manager will automatically create'},
                        {'name':'Module2','module_id':'2','descr':'The layout manager will automatically create'},
                        {'name':'Module3','module_id':'3','descr':'The layout manager will automatically create'},
                        {'name':'Module4','module_id':'4','descr':'The layout manager will automatically create'},
                        {'name':'Module5','module_id':'5','descr':'The layout manager will automatically create'},
                        {'name':'Module6','module_id':'6','descr':'The layout manager will automatically create'},
                        {'name':'Module7','module_id':'7','descr':'The layout manager will automatically create'},
                        {'name':'Module8','module_id':'8','descr':'The layout manager will automatically create'}
                    ]})";
        $this->getResponse()->setBody($blocks);
     }
}
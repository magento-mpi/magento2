<?php

class BlockController extends Mage_Core_Controller_Zend_Admin_Action
{
    function indexAction()
    {
        
    }
    
    function loadTreeAction() {
        $this->getResponse()->setBody($this->_view->render('core/block.tree.phtml'));
    }
    
    function nodeAction() {
        $blocks = '[
            {"text":"Block3","id":"3","cls":"folder","leaf":"true"},
            {"text":"Block4","id":"4","cls":"folder","leaf":"true"},
            {"text":"Block5","id":"5","cls":"folder","leaf":"true"},
            {"text":"Block6","id":"6","cls":"folder","leaf":"true"},
            {"text":"Block7","id":"7","cls":"folder","leaf":"true"},
            {"text":"Block8","id":"8","cls":"folder","leaf":"true"},
            {"text":"Block9","id":"9","cls":"folder","leaf":"true"},
            {"text":"Block10","id":"10","cls":"folder","leaf":"true"},
            {"text":"Block11","id":"11","cls":"folder","leaf":"true"},
            {"text":"Block12","id":"12","cls":"folder","leaf":"true"}
        ]';
        $this->getResponse()->setBody($blocks);
    }
    
    function getBlocksAction() {
        $blocks = "({'totalRecords':'8',
                    'blocks':[{'name':'Block1','block_id':'1','descr':'The layout manager will automatically create'},
                        {'name':'Block2','block_id':'2','descr':'The layout manager will automatically create'},
                        {'name':'Block3','block_id':'3','descr':'The layout manager will automatically create'},
                        {'name':'Block4','block_id':'4','descr':'The layout manager will automatically create'},
                        {'name':'Block5','block_id':'5','descr':'The layout manager will automatically create'},
                        {'name':'Block6','block_id':'6','descr':'The layout manager will automatically create'},
                        {'name':'Block7','block_id':'7','descr':'The layout manager will automatically create'},
                        {'name':'Block8','block_id':'8','descr':'The layout manager will automatically create'}
                    ]})";
        $this->getResponse()->setBody($blocks);
    }
   
}
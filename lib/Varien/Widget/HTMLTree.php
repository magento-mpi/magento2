<?php

Zend_Loader::loadClass('Varien_Widget_Abstract');

class Varien_Widget_HTMLTree extends Varien_Widget_Abstract
{
    private $data = array();
    private $htmlId = 'HTMLTree';

    function __construct(Varien_Db_Tree_NodeSet $data) {
        $this->data = $data;
    }

    /**
     * Set Id for root tag of tree widget
     *
     * @param string $string
     * @return Varien_Widget_HTMLTree
     */
    function setHtmlId($string) {
        $this->htmlId = $string;
        return $this;
    }

    private function renderNode(Varien_Db_Tree_Node $node) {
           return '<a href="#">'.$node->getData('title').'</a>';
    }

    public function renderJS() {
        return null;
        $out = '<script language="javascript" type="text/javascript">'."\r\n";
        $out .= $this->htmlId.'VarienHTMLTree = new VarienTree("'.$this->htmlId.'");'."\r\n";
        $out .= $this->htmlId.'VarienHTMLTree.init();'."\r\n";
        $out .= $this->renderDragNDrop();
        $out .= '</script>'."\r\n";
        return $out;
    }
    
    public function renderDragNDrop() {
        $out = '';
        foreach($this->data as $node) {
            if ($node->getLeft() != 1) {
              //  $out .= 'new Draggable("'.$this->htmlId.':'.$node->getId().'",{revert:true});'."\r\n";
            }
        }
        return $out;
    }
    
    public function render() {
        $out = '<ul class="tree" id="'.$this->htmlId.'">';
        $prevlevel = 0;
        foreach($this->data as $node) {
            
            if ($prevlevel > $node->getLevel()) {
                $out .= str_repeat('</ul></li>',$prevlevel - $node->getLevel());
            }
            
            $out .= '<li id="'.$this->htmlId.':'.$node->getId().'" left="'.$node->getLeft().'" right="'.$node->getRight().'">'.$this->renderNode($node);
            
            if ($node->hasChild) {
                $out .= '<ul>';
            } else {
                $out .= '</li>';
            }
            
            $prevlevel = $node->getLevel();
        }
        
        if ($prevlevel > 0) {
            $out .= str_repeat('</ul></li>',$prevlevel);
        }
        
        $out .= '</ul>';
        
       // $out .= $this->renderJS();
        
        return $out;
    }
}
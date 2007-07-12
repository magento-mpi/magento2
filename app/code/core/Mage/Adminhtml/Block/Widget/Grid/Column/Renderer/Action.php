<?php
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
		$actions = $this->getColumn()->getActions();
		if( !is_array($actions) ) {
		    return;
		}

        foreach($actions as $action){
        	if( is_array($action) ) {
                $this->_toHtml($action);
        	}
        }
    }

    protected function _toHtml($action)
    {
        $actionAttributes = new Varien_Object();

        foreach( $action as $attibute => $value ) {
            switch ($attibute) {
            	case 'confirm':
            	    $action['onclick'] = 'confirm("' . $action['confirm'] . '");';
            	    unset($action['confirm']);
            		break;

            	case 'url':
            	    $urlObject->setFormat($action['url']);
            	    $action['href'] = parent::render($urlObject);
            	    unset($action['url']);
            		break;

            	case 'caption':
            	    $actionCaption = $action['caption'];
            	    unset($action['caption']);
            	    break;
            }
        }

        $actionAttributes->setData($action);
        echo '<a ' . $actionAttributes->serialize() . '>' . $actionCaption . '</a> ';
    }
}
<?php
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
		$actions = $this->getColumn()->getActions();
		if( !is_array($actions) ) {
		    return;
		}

		echo '<span class="nowrap">';
        foreach($actions as $action){
        	if( is_array($action) ) {
                $this->_toHtml($action, $row);
        	}
        }
		echo '</span>';
    }

    protected function _toHtml($action, $row)
    {
        $actionAttributes = new Varien_Object();

        foreach( $action as $attibute => $value ) {
    	    $row->setFormat($action[$attibute]);
    	    $action[$attibute] = parent::render($row);
            switch ($attibute) {
            	case 'confirm':
            	    $action['onclick'] = 'return confirm(\'' . addslashes($action['confirm']) . '\');';
            	    unset($action['confirm']);
               		break;

            	case 'caption':
            	    $actionCaption = $action['caption'];
            	    unset($action['caption']);
               		break;

            	case 'url':
            	    $action['href'] = $action['url'];
            	    unset($action['url']);
               		break;
            }
        }

        $actionAttributes->setData($action);
        echo '<a ' . $actionAttributes->serialize() . '>' . $actionCaption . '</a> ';
    }
}
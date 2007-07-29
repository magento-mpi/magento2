<?php
/**
 * Grid column widget for rendering action grid cells
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{

    public function render(Varien_Object $row)
    {
		$actions = $this->getColumn()->getActions();
		if ( empty($actions) || !is_array($actions) ) {
		    return '&nbsp';
		}
		$out = '<span class="nowrap">';
		$i = 0;
        foreach ($actions as $action){
            $i++;
        	if ( is_array($action) ) {
                $out .= $this->_toHtml($action, $row);
        	}
        	if ( $i < count($actions) ) {
        	    $out .= $this->_showDelimiter();
        	}
        }
		$out .= '</span>';
		return $out;
    }

    protected function _toHtml($action, $row)
    {
        $actionAttributes = new Varien_Object();

        foreach ( $action as $attibute => $value ) {
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
        return '<a ' . $actionAttributes->serialize() . '>' . $actionCaption . '</a>';
    }

    protected function _showDelimiter()
    {
        return '<span class="separator">&nbsp;|&nbsp;</span>';
    }

}
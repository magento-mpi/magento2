<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            if(isset($action[$attibute]) && !is_array($action[$attibute])) {
                $this->getColumn()->setFormat($action[$attibute]);
                $action[$attibute] = parent::render($row);
            } else {
                $this->getColumn()->setFormat(null);
            }

    	    switch ($attibute) {
            	case 'confirm':
            	    $action['onclick'] = 'return confirm(\'' . addslashes($this->htmlEscape($action['confirm'])) . '\');';
            	    unset($action['confirm']);
               		break;

            	case 'caption':
            	    $actionCaption = $action['caption'];
            	    unset($action['caption']);
               		break;

            	case 'url':
            	    if(is_array($action['url'])) {
            	        $params = array($action['field']=>$this->_getValue($row));
            	        if(isset($action['url']['params'])) {
                            $params = array_merge($action['url']['params'], $params);
                	    }
                	    $action['href'] = $this->getUrl($action['url']['base'], $params);
                	    unset($action['field']);
            	    } else {
            	        $action['href'] = $action['url'];
            	    }
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
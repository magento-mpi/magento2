<?php
/**
 * Category form input image element
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Form_Image extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

/*
    protected function _getStructuredArray($associativeArray)
    {
        $outputArray = array();
        if (is_array($associativeArray)) {
            foreach ($associativeArray as $element) {
                $outputArray[$element['image_id']][$element['type']]=array('id'=>$element['id'], 'image'=>$element['image'], 'position'=>$element['position']);
            }
            return $outputArray;
        }
        else {
            return false;
        }
    }
*/

    public function getElementHtml()
    {
        $html = '<table id="image_list" border="0" cellspacing="3" cellpadding="0" width="80%">';
        $html .= '<tr><td valign="middle" align="center">Big Image</td><td valign="middle" align="center">Thumbnail</td><td valign="middle" align="center">Small Thumb</td><td valign="middle" align="center">Sort Order</td><td valign="middle" align="center">Delete</td></tr>';

//        $images = $this->_getStructuredArray($this->getValue());

//        if (is_array($images)) {
            foreach ((array)$this->getValue() as $image) {
                $html .= '<tr>';
                foreach ($this->getValue()->getImageTypes() as $type) {
                    $url = $image->setType($type)->getSourceUrl();
                    $html .= '<td align="center" style="vertical-align:bottom;">';
                    $html .= '<a href="'.$url.'" target="_blank" onclick="imagePreview(\''.$this->getHtmlId().'_image_'.$type.'_'.$image->getValueId().'\');return false;"><img
                        src="'.$url.'" alt="'.$image->getValue().'" height="25" align="absmiddle" class="small-image-preview"></a><br/>';
                    $html .= '<input type="file" name="'.$this->getName().'_'.$type.'['.$image->getValueId().']" size="1"></td>';
                    $html .= '<div id="'.$this->getHtmlId().'_image_'.$type.'_'.$image->getValueId().'" style="display:none" class="image-preview"><img src="'.$url.'" alt="'.$image->getValue().'"></div>';
                }
                $html .= '<td align="center" style="vertical-align:bottom;"><input type="input" name="'.parent::getName().'[position]['.$image->getValueId().']" value="'.$image->getPosition().'" id="'.$this->getHtmlId().'_position_'.$image->getValueId().'" size="3"/></td>';
                $html .= '<td align="center" style="vertical-align:bottom;"><input type="checkbox" name="'.parent::getName().'[delete]['.$image->getValueId().']" value="'.$image->getValueId().'" id="'.$this->getHtmlId().'_delete_'.$image->getValueId().'"/></td>';
                $html .= '</tr>';
            }

//        }

        $html .= '<tr>';
//          $html .= '<td valign="middle" align="left" colspan="3"><input id="'.$this->getHtmlId().'" name="'.$this->getName().'" value="" '.$this->serialize($this->getHtmlAttributes()).' size="20"/></td>';
        $html .= '<td valign="middle" align="left" colspan="3"><a href="#" onclick="addNewImg();return false;">Add New Image</a></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>'."\n";

        $html .= '</table>';

/*
        $html .= '<script language="javascript">
                    var multi_selector = new MultiSelector( document.getElementById( "image_list" ),
                    "'.$this->getName().'",
                    -1,
                        \'<a href="file:///%file%" target="_blank" onclick="imagePreview(\\\''.$this->getHtmlId().'_image_new_%id%\\\');return false;"><img src="file:///%file%" width="50" align="absmiddle" class="small-image-preview" style="padding-bottom:3px; width:"></a> <div id="'.$this->getHtmlId().'_image_new_%id%" style="display:none" class="image-preview"><img src="file:///%file%"></div>\',
                        "",
                        \'<input type="file" name="'.parent::getName().'[new_image][%id%][%j%]" size="1">\'
                    );
                    multi_selector.addElement( document.getElementById( "'.$this->getHtmlId().'" ) );
                    </script>';
*/

        $name = $this->getName();

        $html .= <<<EndSCRIPT

        <script language="javascript">
        id = 0;

        function addNewImg(){

            id--;
            new_file_input = '<input type="file" name="{$name}_%j%[%id%]" size="1">';

		    // Sort order input
		    var new_row_input = document.createElement( 'input' );
		    new_row_input.type = 'text';
		    new_row_input.name = 'general[image][position]['+id+']';
		    new_row_input.size = '3';
		    new_row_input.value = '0';

		    // Delete button
		    var new_row_button = document.createElement( 'input' );
		    new_row_button.type = 'checkbox';
		    new_row_button.value = 'Delete';

            table = document.getElementById( "image_list" );

            // no of rows in the table:
            noOfRows = table.rows.length;

            // no of columns in the pre-last row:
            noOfCols = table.rows[noOfRows-2].cells.length;

            // insert row at pre-last:
            var x=table.insertRow(noOfRows-1);

            // insert cells in row.
            for (var j = 0; j < noOfCols; j++) {

                newCell = x.insertCell(j);
                newCell.align = "center";
                newCell.valign = "middle";

                if (j==3) {
		            newCell.appendChild( new_row_input );
                }
                else if (j==4) {
		            newCell.appendChild( new_row_button );
                }
                else {
                    newCell.innerHTML = new_file_input.replace(/%j%/g, j).replace(/%id%/g, id);
                }

            }

		    // Delete function
		    new_row_button.onclick= function(){

                this.parentNode.parentNode.parentNode.removeChild( this.parentNode.parentNode );

			    // Appease Safari
			    //    without it Safari wants to reload the browser window
			    //    which nixes your already queued uploads
			    return false;
		    };

	    }
        </script>

EndSCRIPT;

        return $html;
    }

    public function getName()
    {
        return  $this->getData('name');
    }
}

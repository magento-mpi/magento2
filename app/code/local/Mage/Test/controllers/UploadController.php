<?php
/**
 * 
 *
 * @file        UploadController.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Test_UploadController extends Mage_Core_Controller_Front_Action
{ 
    public function saveAction()
    {
        if( count($_FILES) == 0 ) {
            return;
        }
        /*
        foreach ($_FILES['files'] as $k => $l) {
            foreach ($l as $i => $v) {
                $files[$i][$k] = $v;
            }
        }
        */
        $u = new Varien_File_Uploader($_FILES['filename']);
        $u->save(Mage::getBaseDir('upload'));
    }
}
// ft:php
// fileformat:unix
// tabstop:4
?>

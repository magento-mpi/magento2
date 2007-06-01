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

        $result = new Varien_Object();

        $uploadFile = new Varien_File_Uploader($_FILES['filename']);
        $uploadFile->save(Mage::getBaseDir('upload'));
        if( $uploadFile->getError() != "" ) {
            $result->setError( $uploadFile->getError() );
        } else {
            $result->setPath( $uploadFile->getDestinationPath() );
            $result->setName( $uploadFile->getFileName() );
            $result->setSize( $uploadFile->getFileSize() );
        }
        header("Content-type: text/xml");
        $this->getResponse()->setBody($result->toXml(array(), "file", true, false));
    }
}
// ft:php
// fileformat:unix
// tabstop:4
?>

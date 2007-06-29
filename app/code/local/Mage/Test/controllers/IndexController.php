<?php
/**
 *
 *
 * @file        IndexController.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Test_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        // Load default layout
        /*
        $collection = Mage::getResourceSingleton('log/visitor_collection')
            #->getTimeline(90)
            #->applyDateRange('2007-01-01', '2007-12-12')
            ->useOnlineFilter()
            ->load(true);

        foreach( $collection -> getItems() as $item ) {
            $item->setLocation(long2ip($item->getRemoteAddr()))
                 ->addCustomerData($item);

            if( $item->getCustomerId() > 0 ) {
                //print_r( $item );
                $item->setFullName( $item->getCustomerData()->getName() );

                // Not needed yet...
                // $adresses = $item->getCustomerData()->getAddressCollection()->getPrimaryAddresses();
            } else {
                $item->setFullName('Guest');
            }
        }

        print "<pre>debug: \n";
        print_r($collection);
        print "</pre>\n";

        $block = $this->getLayout()->createBlock('core/template', 'upload');
        $block->settemplate('test/index.phtml');

        $this->getResponse()->setBody($block->toHtml());
        
        $aggregator = Mage::getSingleton('log/visitor_aggregator');
        $aggregator->update();
        */
    }

    public function pagesAction()
    {
        $collection = Mage::getResourceSingleton('cms/page_collection')
            ->load(true);
        echo "<pre>";
        print_r($collection);
        echo "</pre>";
    }

    public function uploadAction()
    {

    }

    public function testAction()
    {
        if( intval($this->getRequest()->getParam('do_upload')) == 1 ) {
            foreach ($_FILES['my_field'] as $k => $l) {
                foreach ($l as $i => $v) {
                    if (!@array_key_exists($i, $files))
                        $files[$i] = array();
                    $files[$i][$k] = $v;
                }
            }

            foreach( $files as $file ) {
                $u = new Varien_File_Uploader($file);
                $u->upload("/var/www/magento/uploads/");
            }

            die();
        }
    }

    public function imageAction()
    {
        $u = new Varien_Image( Varien_Image_Adapter::ADAPTER_GD2, Mage::getBaseDir('upload')."/svin_0.jpg" );
        $u->open();
        $u->setImageBackgroundColor(5);
        $u->rotate(45);
        $u->resize(null, 1500);
        $u->crop(0,0,0,0);
        $u->watermark(Mage::getBaseDir('upload')."/watermark.png", 0, 0, 5, true);

        $u->save(null, "MyTest123.jpg");
    }

}
// ft:php
// fileformat:unix
// tabstop:4
?>

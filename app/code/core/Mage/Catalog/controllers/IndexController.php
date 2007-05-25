<?php



class Mage_Catalog_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Index action
     *
     * Display categories home page
     *
     */
    function indexAction() 
    {
        $this->loadLayout();

        $homeBlock = $this->getLayout()->createBlock('tpl', 'homecontent')->setTemplate('catalog/home.phtml');
        $this->getLayout()->getBlock('content')->append($homeBlock);

        $this->renderLayout();
    }

    function importAction() 
    {
        $fileName = Mage::getBaseDir('var').DS.'import'.DS.'simplybirkenstock.txt';
        
        $fieldMap = array(
            0=>'old_id',
            1=>'sku',
            2=>'small_image', 
            3=>'image',
            4=>'price',
            5=>'weight',
            6=>'status',
            7=>'manufacturer',
            8=>'shoe_type',
            9=>'is_clearance',
            10=>'is_shoe',
            11=>'product_type',
            12=>'name',
            13=>'description',
        );
        
        echo "<pre>";
        
        $import = new Mage_Catalog_Import();
        $import->loadCsv($fileName, $fieldMap);
        $import->convert()->save();
    }
}


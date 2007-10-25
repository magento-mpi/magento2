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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */




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

        $homeBlock = $this->getLayout()->createBlock('core/template', 'homecontent')->setTemplate('catalog/home.phtml');
        $this->getLayout()->getBlock('content')->append($homeBlock);

        $this->renderLayout();
    }

    function testAction()
    {
        /*
    	$content = Mage::getModel('catalogexcel/export')->getWorkbookXml();

    	$fileName = 'catalog.xml';
        header('HTTP/1.1 200 OK');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Last-Modified: '.date('r'));
        header("Accept-Ranges: bytes");
        header("Content-Length: ".strlen($content));
        header("Content-type: application/octet-stream");
        echo $content;
        */
        Mage::getSingleton('catalog/convert')->run('export_catalog_to_http');
        #Mage::getSingleton('catalog/convert')->run('export_catalog');
        #Mage::getSingleton('catalog/convert')->run('import_catalog');

    	exit;
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

    protected function _isAllowed()
    {
        print "Action: ".$this->getRequest()->getActionName() . "<br/>";
    	/*switch ($this->getRequest()->getActionName()) {
            case 'pending':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/pending');
                break;
            case 'all':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/all');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag');
                break;
        }
        */
    }
}


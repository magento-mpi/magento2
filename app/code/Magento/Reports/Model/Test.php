<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Model  for flex reports
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Reports_Model_Test extends Magento_Object
{

    public function getUsersCountries( )
    {
        return file_get_contents( Mage::getModuleDir('etc','Magento_Reports').DS.'flexTestDataCountries.xml' );
    }

    public function getUsersCities( $countryId )
    {
        $dom = new DOMDocument();
        $dom -> preserveWhiteSpace = false;
        $dom -> load( Mage::getModuleDir('etc','Magento_Reports').DS.'flexTestDataCities.xml' );

        $root = $dom -> documentElement;
        $rows = $root -> getElementsByTagName( 'row' );

        $childsToRemove = array();
        for( $i = 0; $i < $rows -> length; $i++)
        {
            for( $j = 0; $j < $rows -> item($i) -> childNodes -> length; $j ++ )
                if(
                    $rows -> item($i) -> childNodes -> item($j) -> nodeType == XML_ELEMENT_NODE
                        &&
                    $rows -> item($i) -> childNodes -> item($j) -> nodeName == 'countryId'
                        &&
                    $rows -> item($i) -> childNodes -> item($j) -> nodeValue != $countryId
                )
                    $childsToRemove[] = $rows -> item($i);
        }

        foreach( $childsToRemove as $child )
            $root -> removeChild( $child );

        return $dom -> saveXML();
    }

    public function getTimelineData( )
    {
        return file_get_contents( Mage::getModuleDir('etc','Magento_Reports').DS.'flexTestDataTimeline.xml' );
    }

    public function getAllLinearExample( )
    {
        $session = Mage::getModel('Magento_Reports_Model_Session');

        $startPoint = time() - 24*60*60;

        $allData = array();
        $countOfStartData = 12;
        for($i = 1; $i<= $countOfStartData; $i++)
        {
            $allData[] = array( 'time'=>date("Y-m-d H:i",$startPoint), 'value'=>rand(1, 100) );
            $startPoint += 30*60;
        }

        $allData[] = array( 'time'=>date("Y-m-d H:i",$startPoint+(90*60)));

        $session -> setData('startPoint', $startPoint);

        return $this -> returnAsDataSource( $allData );
    }

    public function getNewLinearData()
    {
        $session = Mage::getModel('Magento_Reports_Model_Session');


        $startPoint = $session -> getData('startPoint');

        $reset = 12;


        $newData  = array(
            array( 'time'=> date("Y-m-d H:i", $startPoint), 'value'=>rand(1, 100) )
        );

        $startPoint += 30*60;
        $newData[]  = array( 'time'=> date("Y-m-d H:i", $startPoint+(90*60)) );

        $session -> setData('startPoint', $startPoint);

        return $this -> returnAsDataSource( $newData, $reset );
    }

    private function returnAsDataSource( &$array , $reset = 0)
    {
        $dom = new DOMDocument();
        $dom -> preserveWhiteSpace = false;
        $dom -> loadXML( "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n<dataSource></dataSource>" );
        $root = $dom ->documentElement;
        if($reset)
        {
            $resetItem = $dom -> createElement("reset");
            $resetItem -> nodeValue = $reset;
            $root->appendChild($resetItem);
        }
        foreach($array  as $item )
        {
            $row = $dom->createElement('row');
            foreach( $item as $key => $val)
            {
                $valItem = $dom->createElement( $key );
                $valItem->nodeValue = $val;
                $row->appendChild($valItem);
            }

            $root->appendChild($row);
        }

        return $dom->saveXML();
    }
}

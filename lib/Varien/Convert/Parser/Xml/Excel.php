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
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert excel xml parser
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Varien_Convert_Parser_Xml_Excel extends Varien_Convert_Parser_Abstract
{
    public function parse()
    {
        
    }
    
    public function unparse()
    {
        $xml = '<'.'?xml version="1.0"?'.'><'.'?mso-application progid="Excel.Sheet"?'.'>
<Workbook xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
  
        $workbook = $this->getData();
        if (is_array($workbook)) {
            foreach ($workbook as $worksheetName=>$worksheet) {
                if (!is_array($worksheet)) {
                    continue;
                }
                $xml .= '<Worksheet ss:Name="'.$worksheetName.'"><ss:Table>';
                foreach ($worksheet as $i=>$row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    if ($i==0) {
                        $xml .= '<ss:Row>';
                        foreach ($row as $fieldName=>$data) {
                            $xml .= '<ss:Cell><Data ss:Type="String">'.$fieldName.'</Data></ss:Cell>';
                        }
                        $xml .= '</ss:Row>';
                    }
                    $xml .= '<ss:Row>';
                    foreach ($row as $data) {
                        $xml .= '<ss:Cell><Data ss:Type="String">'.$data.'</Data></ss:Cell>';
                    }
                    $xml .= '</ss:Row>';
                }
                $xml .= '</ss:Table></Worksheet>';
            }
        }
  
        $xml .= '</Workbook>';
        
        $this->setData($xml);
        
        return $this;
    }
}
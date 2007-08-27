<?php

class Mage_CatalogExcel_Model_Export
{		
	public function getWorkbookXml()
	{
		$res = Mage::getResourceModel('catalogexcel/export');
		
		$xml = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook
  xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
'.$this->getWorksheetXml('Products', $res->fetchProducts()).'
'.$this->getWorksheetXml('Categories', $res->fetchCategories()).'
'.$this->getWorksheetXml('Image Gallery', $res->fetchImageGallery()).'
'.$this->getWorksheetXml('Product Links', $res->fetchProductLinks()).'
'.$this->getWorksheetXml('Products in Categories', $res->fetchProductsInCategories()).'
'.$this->getWorksheetXml('Products in Stores', $res->fetchProductsInStores()).'
'.$this->getWorksheetXml('Attributes', $res->fetchAttributes()).'
'.$this->getWorksheetXml('Attribute Sets', $res->fetchAttributeSets()).'
'.$this->getWorksheetXml('Attribute Options', $res->fetchAttributeOptions()).'
</Workbook>';
		
		return $xml;
	}
	
	public function getWorksheetXml($name, $data)
	{
		$xml = '<Worksheet ss:Name="'.$name.'"><ss:Table>';
		foreach ($data as $i=>$row) {
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
		
		return $xml;
	}
}
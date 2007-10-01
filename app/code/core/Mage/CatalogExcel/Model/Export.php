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
 * @package    Mage_CatalogExcel
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogExcel_Model_Export
{
	public function getWorkbookXml()
	{
		$res = Mage::getResourceModel('catalogexcel/export');

		$xml = '<'.'?xml version="1.0"?'.'>
<'.'?mso-application progid="Excel.Sheet"?'.'>
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
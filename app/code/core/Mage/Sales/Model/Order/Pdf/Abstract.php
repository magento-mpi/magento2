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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment method abstract model
 *
 * @author Yuriy Scherbina <yuriy.scherbina@varien.com>
 */
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
{
    abstract public function getPdf();

    /**
* Returns the total width in points of the string using the specified font and
* size.
*
* This is not the most efficient way to perform this calculation. I'm
* concentrating optimization efforts on the upcoming layout manager class.
* Similar calculations exist inside the layout manager class, but widths are
* generally calculated only after determining line fragments.
*
* @param string $string
* @param Zend_Pdf_Resource_Font $font
* @param float $fontSize Font size in points
* @return float
*/
    function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }
}
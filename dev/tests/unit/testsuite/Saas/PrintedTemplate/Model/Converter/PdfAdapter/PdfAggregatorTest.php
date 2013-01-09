<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once 'Saas/PrintedTemplate/Model/Converter/PdfAdapter/PdfAggregator.php';
require_once 'Saas/PrintedTemplate/Model/Converter/PdfAdapter/PdfAggregatorInterface.php';

class Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregatorTest
    extends PHPUnit_Framework_TestCase
{
    public function testShouldImplementPdfAggregatorInterface()
    {
        $this->assertInstanceOf(
            'Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregatorInterface',
            new Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregator
        );
    }

    public function testRenderShouldMergeGivenPdfFilesInOneFile()
    {
        // prepare
        $texts = array();
        for ($i = 0; $i < 3; $i++) {
            $texts[] = uniqid();
        }

        $documents = array_map(
            function ($text) {
                $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 36);
                $page->drawText($text, 0, 0);
                $pdf = new Zend_Pdf;
                $pdf->pages[] = $page;

                return $pdf->render();
            },
            $texts
        );

        $aggregator = new Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregator;

        // act
        $result = $aggregator->render($documents);
        $pdfResult = Zend_Pdf::parse($result);

        // assert
        $this->assertCount(count($texts), $pdfResult->pages);

        foreach ($texts as $text) {
            $this->assertContains($text, $result);
        }
    }

    /**
     * @expectedException Exception
     * @dataProvider providerIncorrectDocuments
     */
    public function testRenderShouldThrowExceptionOnIncorrectArgument(array $incorrectDocuments)
    {
        $aggregator = new Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregator;
        $aggregator->render($incorrectDocuments);
    }

    public function providerIncorrectDocuments()
    {
        return array(
            array(array(1, 'asdasd', 7)),
            array(array(new stdClass, array(78, 'assdffss'), 'sqqq')),
        );
    }
}

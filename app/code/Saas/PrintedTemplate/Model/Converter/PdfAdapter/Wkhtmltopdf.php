<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * WkHtmlToPdf library renderer
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Converter_PdfAdapter_Wkhtmltopdf
    implements Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface
{
    /**
     * Path to config with path to binary file of Wkhtmltopdf library
     */
    const XML_PATH_WKHTMLTOPDF_BINARY = 'sales_pdf/saas_printedtemplate/wkhtmltopdf/binary_file_path';

    /**
     * Default page size if isn't specified
     */
    const DEFAULT_PAGE_SIZE = 'A4';

    /**
     * Path to flag which shows if dynamic header|footer height feature enabled
     */
    const XML_PATH_DYNAMIC_HEIGHT_ENABLED = 'sales_pdf/saas_printedtemplate/wkhtmltopdf/dynamic_header_footer_enabled';

    /**
     * Page size
     *
     * @var Saas_PrintedTemplate_Model_PageSize|null
     */
    private $_pageSize;

    /**
     * Page ortientation
     *
     * @var string PAGE_ORIENTATION_PORTRAIT|PAGE_ORIENTATION_PORTRAIT|null
     */
    private $_pageOrientation;

    /**
     * Header html
     *
     * @var string
     */
    private $_headerHtml = '';

    /**
     * Header height
     *
     * @var Zend_Measure_Length
     */
    private $_headerHeight;

    /**
     * Footer html
     *
     * @var string
     */
    private $_footerHtml = '';

    /**
     * Footer height
     *
     * @var Zend_Measure_Length
     */
    private $_footerHeight;

    /**
     * Set header settings
     *
     * @param string $html
     * @param Zend_Measure_Length $height
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Wkhtmltopdf Self
     */
    public function setupHeader($html, Zend_Measure_Length $height = null)
    {
        $this->_headerHtml = $html;
        $this->_headerHeight = $height;

        return $this;
    }

    /**
     * Set footer settings
     *
     * @param string $html
     * @param Zend_Measure_Length $height
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Wkhtmltopdf Self
     */
    public function setupFooter($html, Zend_Measure_Length $height = null)
    {
        $this->_footerHtml = $html;
        $this->_footerHeight = $height;

        return $this;
    }

    /**
     * Convert HTML code to PDF code using WkHtmlToPdf library
     *
     * @param string $html
     * @return string
     * @throws Magento_Core_Exception
     */
    public function renderHtml($bodyHtml, Saas_PrintedTemplate_Model_PageSize $pageSize = null, $pageOrientation = null)
    {
        $this->_setPageSize($pageSize);
        $this->_setPageOrientation($pageOrientation);

        $headerFileName = '';
        $footerFileName = '';

        $path = Mage::getStoreConfig(self::XML_PATH_WKHTMLTOPDF_BINARY);

        if (!file_exists($path)) {
            Mage::throwException(Mage::helper('Saas_PrintedTemplate_Helper_Data')->__(
                'Incorrect path to wkhtmltopdf binary; fix it in configuration file.'
            ));
        }
        if ($this->_headerHtml) {
            $headerFileName = $this->_makeTempHtmlFile(
                $this->_prepareHeaderFooterHtml($this->_headerHtml),
                'pdfheader'
            );
        }
        if ($this->_footerHtml) {
            $footerFileName = $this->_makeTempHtmlFile(
                $this->_prepareHeaderFooterHtml($this->_footerHtml),
                'pdffooter'
            );
        }

        $bodyHtml = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="margin: 0; padding: 0;">
    $bodyHtml
</body>
</html>
EOT;
        $result = $this->_pipeExec(
            escapeshellarg($path) . ' ' . $this->_getParams($headerFileName, $footerFileName) . ' - -',
            $bodyHtml
        );
        @unlink($headerFileName);
        @unlink($footerFileName);

        if ($result['return'] > 0) {
            $exception = new Exception('PDF conversion error: ' . $result['stderr']);
            Mage::logException($exception);
            Mage::throwException(Mage::helper('Saas_PrintedTemplate_Helper_Data')->__(
                'Cannot generate PDF for current template. Please, check the template.'
            ));
        }

        return $result['stdout'];
    }

    /**
     * Add js to replace wkhtmltopdf footer and headers placeholders
     *
     * @param string $html
     * @return string
     */
    protected function _prepareHeaderFooterHtml($html)
    {
        // make header/footer margins same as content
        $marginLeft = '8px';
        $marginRight = '8px';

        return <<<HTML
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <script>
        /**
         * Parse query string to object
         *
         * @return object
         */
        function getQueryStringParams()
        {
            // keeps params passed throught query string
            var data = {};
            // parse query string params
            var qsParts = document.location.search.substring(1).split('&');
            for (var i in qsParts) {
                var keyval = qsParts[i].split('=',2);
                data[keyval[0]] = unescape(keyval[1]);
            }

            return data;
        }

        /**
         * Fill variable containers with query string parameters
         *
         * @return void
         */
        function fillVariables()
        {
            var data = getQueryStringParams();

            // variables to process
            var vars = {
                'page': 'printed_template_page_number',
                'topage': 'printed_template_page_total'
            };
            for (var varName in vars) {
                var containers = document.getElementsByClassName(vars[varName]);
                for (var j = 0; j < containers.length; ++j) {
                    containers[j].textContent = data[varName];
                }
            }
        }
        </script>
    </head>
    <body style='border:0; margin: 0px $marginRight 0px  $marginLeft; padding: 0;' onload='fillVariables()'>
        $html
    </body>
</html>
HTML;
    }

    /**
     * Make temporary html file use it for headers/footers generation
     *
     * @param string $content File content
     * @param string $prefix Temporary file name prefix
     *
     * @return string Created file's name
     */
    protected function _makeTempHtmlFile($content, $prefix = '')
    {
        $tempDir = Mage::getBaseDir('var') . DS . 'pdf_printouts';
        if (!is_dir($tempDir) && !mkdir($tempDir)) {
            Mage::throwException('Cannot make temporary directory for html file');
        }

        $tempFileName = Mage::helper('Magento_Core_Helper_Data')->uniqHash($prefix) . '.html';
        $htmlFilePath = $tempDir . DS . $tempFileName;

        $result = file_put_contents($htmlFilePath, $content);
        if ($result === false) {
            Mage::throwException('Cannot make temporary html file');
        }

        return $htmlFilePath;
    }


    /**
     * Set size of rendered page
     *
     * @param Saas_PrintedTemplate_Model_PageSize $size
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Wkhtmltopdf Self
     */
    protected function _setPageSize(Saas_PrintedTemplate_Model_PageSize $size = null)
    {
        $this->_pageSize = $size;

        return $this;
    }

    /**
     * Set orientation of rendered page
     *
     * @param string $orientation
     * @throws InvalidArgumentException
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Wkhtmltopdf Self
     */
    protected function _setPageOrientation($orientation = null)
    {
        $incorrect = $orientation !== null
            && $orientation != self::PAGE_ORIENTATION_LANDSCAPE
            && $orientation != self::PAGE_ORIENTATION_PORTRAIT;

        if ($incorrect) {
            throw new InvalidArgumentException('Incorrect page orientation.');
        }

        $this->_pageOrientation = $orientation;

        return $this;
    }

    /**
     * Execute Wkhtmltopdf libray's binary file and send HTML using pipes.
     *
     * @param string $command
     * @param string $input
     * @return array
     */
    protected function _pipeExec($command, $input = '')
    {
        $process = proc_open($command,
            array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ),
            $pipes
        );

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        return array(
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $returnCode
        );
    }

    /**
     * Prepare parameters for wkhtmltopdf call
     *
     * @return string
     */
    protected function _getParams($headerPath = '', $footerPath = '')
    {
        $params = '--encoding utf-8 --quiet ' . $this->_preparePageOrientation() . $this->_preparePageSize()
            . ' --load-error-handling ignore';

        if ($headerPath) {
            if ($this->_headerHeight) {
                $marginTop = $this->_headerHeight
                    ->setLocale('en_US')
                    ->setType(Zend_Measure_Length::MILLIMETER)
                    ->getValue();

                $params .= ' --margin-top ' . $marginTop;
            }

            $params .= ' --header-html ' . $headerPath
                . ' --header-spacing 5'; // avoid header overlaping
        }

        if ($footerPath) {
            if ($this->_footerHeight) {
                $marginBottom = $this->_footerHeight
                    ->setLocale('en_US')
                    ->setType(Zend_Measure_Length::MILLIMETER)
                    ->getValue();

                $params .= ' --margin-bottom ' . $marginBottom;
            }
            $params .= ' --footer-html ' . $footerPath
                . ' --footer-spacing 5'; // avoid footer overlaping
        }

        return $params;
    }

    /**
     * Prepares parameters for page size
     *
     * @return string
     */
    private function _preparePageSize()
    {
        if (!$this->_pageSize || !$this->_pageSize->hasWidth() && !$this->_pageSize->hasHeight()) {
            return ' --page-size ' . self::DEFAULT_PAGE_SIZE;
        }

        $sizeParams = '';
        if ($this->_pageSize->hasWidth()) {
            $width = $this->_pageSize
                ->getWidth()
                ->setType(Zend_Measure_Length::MILLIMETER)
                ->getValue();
            $sizeParams .= ' --page-width ' . $width;
        }
        if ($this->_pageSize->hasHeight()) {
            $height = $this->_pageSize
                ->getHeight()
                ->setType(Zend_Measure_Length::MILLIMETER)
                ->getValue();
            $sizeParams .= ' --page-height ' . $height;
        }

        return $sizeParams;
    }

    /**
     * Prepares parameter for page orientation
     *
     * @return string
     */
    private function _preparePageOrientation()
    {
        $orientation = $this->_pageOrientation != self::PAGE_ORIENTATION_LANDSCAPE ? 'portrait' : 'landscape';

        return " --orientation $orientation";
    }

    /**
     * If Wkhtmltopdf has been patched, then it can calculate header/footer dymanically
     *
     * @return bool
     */
    public function canCalculateHeightsDynamically()
    {
        return Mage::getStoreConfig(self::XML_PATH_DYNAMIC_HEIGHT_ENABLED);
    }
}

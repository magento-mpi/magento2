<?php
/**
 * Import Image Result
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_Result
{
    /**
     * Message indent
     */
    const MESSAGE_INDENT = '&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * @var array
     */
    protected $_result = array(
        'valid' => array(),
        'invalid' => array(),
    );

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @param Saas_ImportExport_Helper_Data $helper
     */
    public function __construct(Saas_ImportExport_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Add valid file
     *
     * @param string $file
     */
    public function addValid($file)
    {
        $this->_result['valid'][] = array(
            'file' => $file,
        );
    }

    /**
     * Add invalid file
     *
     * @param string $file
     * @param string|array $message
     */
    public function addInvalid($file, $message)
    {
        $this->_result['invalid'][] = array(
            'file' => basename($file),
            'message' => is_array($message) ? implode(';<br />' . self::MESSAGE_INDENT, $message) : $message,
        );
    }

    /**
     * Get image errors formatted text
     *
     * @return string
     */
    public function getErrorsAsString()
    {
        $result = '';
        if ($this->_result['invalid']) {
            $indent = self::MESSAGE_INDENT;
            $result = __('Product images errors (next image files will be ignored):');

            foreach ($this->_result['invalid'] as $file) {
                $result .= '<br />' . $indent . $file['message'] . '<br />' . $indent . $indent . $file['file'];
            }
        }
        return $result;
    }

    /**
     * Get upload summary information
     *
     * @return array
     */
    public function getUploadSummary()
    {
        $indent = self::MESSAGE_INDENT;
        $totalValid = count($this->_result['valid']);
        $totalInvalid = count($this->_result['invalid']);
        $totalImages = $totalValid + $totalInvalid;

        if (!$this->_result['invalid'] && $this->_result['valid']) {
            // @codingStandardsIgnoreStart
            $message = __('Image Archive File is valid. All image files successfully uploaded to media storage.');
            // @codingStandardsIgnoreEnd

            $message .= '<br />' . $indent . __('Checked images: ') . $totalImages . '<br />' . $indent
                . __('Valid images: ') . $totalValid;
            return array('is_success' => true, 'message' => $message);

        } elseif ($this->_result['invalid'] && $this->_result['valid']) {
            $message = __('Remainder image files, were successfully uploaded to media storage.')
                . '<br />' . $indent
                . __('Checked images: ') . $totalImages
                . '<br />' . $indent . __('Valid images: ') . $totalValid
                . '<br />' . $indent . __('Invalid images: ') . $totalInvalid;
            return array('is_success' => true, 'message' => $message);

        } else {
            $message = __('There are no valid images in archive')
                . '<br />' . $indent . __('Checked images: ') . $totalImages;
            return array('is_success' => false, 'message' => $message);
        }
    }
}

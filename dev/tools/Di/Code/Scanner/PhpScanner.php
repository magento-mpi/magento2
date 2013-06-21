<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class PhpScanner extends FileScanner
{
    /**
     * Regular expression pattern
     *
     * Searches for factories specified in php files
     *
     * @var string
     */
    protected $_pattern = '/[ \\b\n\'"\(]{1}([A-Z]{1}[a-zA-Z0-9]*_[A-Z]{1}[a-zA-Z0-9_]*(Factory))[ \\b\n\'"]{1}/';

    /**
     * Prepare file content
     *
     * @param string $content
     * @return string
     */
    protected function _prepareContent($content)
    {
        $output  = '';
        $commentTokens = array(T_COMMENT);

        if (defined('T_DOC_COMMENT')) {
            $commentTokens[] = T_DOC_COMMENT;
        }

        $tokens = token_get_all($content);

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    continue;
                }
                $token = $token[1];
            }
            $output .= $token;
        }
        return $output;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Url;

use Magento\View\FileSystem;

/**
 * Helper to work with CSS files
 */
class CssResolver
{
    /**
     * PCRE that matches non-absolute URLs in CSS content
     */
    const REGEX_CSS_RELATIVE_URLS
        = '#url\s*\(\s*(?(?=\'|").)(?!http\://|https\://|/|data\:)(.+?)(?:[\#\?].*?|[\'"])?\s*\)#';

    /**
     * Adjust relative URLs in CSS content as if the file with this content is to be moved to new location
     *
     * @param string $cssContent
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public static function relocateRelativeUrls($cssContent, $from, $to)
    {
        $offset = FileSystem::offsetPath($from, $to);
        $callback = function($path) use ($offset) {
            return FileSystem::normalizePath($offset . '/' . $path);
        };
        return self::replaceRelativeUrls($cssContent, $callback);
    }

    /**
     * A generic method for applying certain callback to all found relative URLs in CSS content
     *
     * Traverse through all relative URLs and apply a callback to each path
     * The $inlineCallback is a user function that obtains the URL value and must return a replacement
     *
     * @param string $cssContent
     * @param callback $inlineCallback
     * @return string
     */
    public static function replaceRelativeUrls($cssContent, $inlineCallback)
    {
        $patterns = self::extractRelativeUrls($cssContent);
        if ($patterns) {
            $replace = array();
            foreach ($patterns as $pattern => $path) {
                if (!isset($replace[$pattern])) {
                    $newPath = $inlineCallback($path);
                    $newPattern = str_replace($path, $newPath, $pattern);
                    $replace[$pattern] = $newPattern;
                }
            }
            if ($replace) {
                $cssContent = str_replace(array_keys($replace), array_values($replace), $cssContent);
            }
        }
        return $cssContent;
    }

    /**
     * Extract all "import" directives from CSS-content and put them to the top of document
     *
     * @param string $cssContent
     * @return string
     */
    public static function aggregateImportDirectives($cssContent)
    {
        $parts = preg_split('/(@import\s.+?;\s*)/', $cssContent, -1, PREG_SPLIT_DELIM_CAPTURE);
        $imports = array();
        $css = array();
        foreach ($parts as $part) {
            if (0 === strpos($part, '@import', 0)) {
                $imports[] = trim($part);
            } else {
                $css[] = $part;
            }
        }

        $result = implode($css);
        if ($imports) {
            $result = implode("\n", $imports)
                . "\n/* The above import directives are aggregated from content. */\n"
                . $result
            ;
        }
        return $result;
    }

    /**
     * Subroutine for obtaining url() fragments from the CSS content
     *
     * @param string $cssContent
     * @return array
     */
    private static function extractRelativeUrls($cssContent)
    {
        preg_match_all(self::REGEX_CSS_RELATIVE_URLS, $cssContent, $matches);
        if (!empty($matches[0]) && !empty($matches[1])) {
            return array_combine($matches[0], $matches[1]);
        }
        return array();
    }
}

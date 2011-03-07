<?php
/**
 * Custom tag class
 * 
 * @see parserTag
 * @package phpDocumentorCustom
 */
class parserTagCustom extends parserTag
{
    /**
     * Tag keyword
     * e.g. issue, testcase
     * @var string
     */
    public $keyword = '_customTag';

    /**
     * Command line options array.
     * Array is in the format of:
     * <pre>
     *   'issue-baseurl' => array(
     *       'tag'  => array('--issue-baseurl'),
     *       'desc' => 'base url for links generated from @issue tag',
     *       'type' => 'value',
     *   );
     * </pre>
     *
     * Also it can be defined in new section of ini-file:
     * <pre>
     * [_phpDocumentor_tag_issue]
     * issue-baseurl = www.localhost.com
     *
     * </pre>
     *
     * @see Io::$phpDocOptions
     * @see Io::Io()
     * @var array
     */
    public $phpDocOptions = array();

    /**
     * Get settings from ini-file or command line
     *
     * @param string $key Settings keyword
     * @return string|false
     */
    public function getSettings($key = false)
    {
        global $_phpDocumentor_setting;

        if (empty($key)) return false;
        $value = false;

        // Ini-file section name
        $ini_array = '_phpDocumentor_tag_' . $this->keyword;
        global ${$ini_array};

        // If there's no such keyword in command line, try to get it from ini
        if (isset($_phpDocumentor_setting[$key])) {
            $value = $_phpDocumentor_setting[$key];
        } else if (isset(${$ini_array}[$key])) {
            $value = ${$ini_array}[$key];
        }

        return $value;
    }

    /**
     * Generate url tag
     * @param string $url
     * @return string|false
     */
    protected function generateUrl($key, $value, $settings_key = false)
    {
        $id = $value->getString();
        $this->value = new parserStringWithInlineTags();
        $this->value->add($id);

        $base_url = $this->prepareUrl($this->getSettings($settings_key));

        if ($base_url) {
            $link = new parserLinkInlineTag($base_url . '/' . $id, $id);
            $string = new parserStringWithInlineTags;
            $string->add($link);
            $this->value = $string;
        }
    }

    /**
     * Prepare url string
     * @param string $url
     * @return string|false
     */
    protected function prepareUrl($url = false)
    {
        if (!$url) return false;

        $url = rtrim($url, '/');

        if (false === strpos($url, 'http://')) {
             $url = 'http://' . $url;
        }

        return $url;
    }
}
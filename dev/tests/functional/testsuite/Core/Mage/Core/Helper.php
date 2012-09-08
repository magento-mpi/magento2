<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for core functionality
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Core_Helper extends Mage_Selenium_TestCase
{
    /**
     * Posts any data to a specific page
     *
     * @param string $targetPage
     * @param array $data
     */
    public function post($page, $data)
    {
        $this->navigate('home_page');
        $url = $this->_getUrlByPage($page);
        $script = $this->_composeEvalScriptForPost($url, $data);
        $this->getEval($script);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
    }

    /**
     * Return page url by page name
     *
     * @param string $page
     */
    protected function _getUrlByPage($page)
    {
        $area = $this->_configHelper->getArea();
        return $this->_uimapHelper->getPageUrl($area, $page, $this->_paramsHelper) . $this->_urlPostfix;
    }

    protected  function _composeEvalScriptForPost($targetUrl, $data)
    {
        $jsonTargetUrl = json_encode($targetUrl);
        $jsonData = json_encode($data);
        $scriptTemplate = <<<JSSCRIPT

(function(url, params) {
    var addValueAsField = function (form, name, value) {
        if (value instanceof Array) {
            for (var i = 0, length = value.length; i < length; i++) {
                addValueAsField(form, name + '[]', value[i]);
            }
        } else {
            var field = window.document.createElement('input');
            field.setAttribute('type', 'hidden');
            field.setAttribute('name', name);
            field.setAttribute('value', value);
            form.appendChild(field);
        }
    }

    var form = window.document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);

    for (var key in params) {
        addValueAsField(form, key, params[key]);
    }

    window.document.body.appendChild(form);
    form.submit();
})(%s, %s);
JSSCRIPT;

        $script = sprintf($scriptTemplate, $jsonTargetUrl, $jsonData);
        return $script;
    }
}

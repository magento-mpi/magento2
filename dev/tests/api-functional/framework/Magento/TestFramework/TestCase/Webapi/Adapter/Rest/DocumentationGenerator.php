<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\TestCase\Webapi\Adapter\Rest;

/**
 * Generator for documentation
 *
 */
class DocumentationGenerator
{
    /**
     * Generate documentation based on request-response data during REST requests.
     *
     * @param string $httpMethod
     * @param string $resourcePath
     * @param array $arguments
     * @param array $response
     */
    public function generateDocumentation($httpMethod, $resourcePath, $arguments, $response)
    {
        $content = $this->generateHtmlContent($httpMethod, $resourcePath, $arguments, $response);
        $filePath = $this->generateFileName($resourcePath);
        if (!is_writable(dirname($filePath))) {
            throw new \RuntimeException('Directory for documentation generation is not writable.');
        }
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $endHtml = $this->generateHtmlFooter();
            $fileContent = str_replace($endHtml, '', $fileContent);
            $content = "{$fileContent}\n{$content}";
            unlink($filePath);
            file_put_contents($filePath, $content, FILE_APPEND);
        } else {
            file_put_contents($filePath, $content, FILE_APPEND);
        }
    }

    /**
     * Prepare html for generated document
     *
     * @param $resourcePath
     * @param $arguments
     * @param $response
     * @return string
     */
    protected function generateHtmlContent($httpMethod, $resourcePath, $arguments, $response)
    {
        if (empty($arguments)){
            $arguments = 'This call does not accept a request body.';
            $requestParametersHtml = '';
        } else {
            $requestParameters = $this->retrieveParametersAsHtml($arguments);
            $arguments = json_encode($arguments, JSON_PRETTY_PRINT);
            $requestParametersHtml = <<<HTML
            <table class="docutils field-list" frame="void" rules="none"  width="400">
                <colgroup>
                    <col width="35%" class="field-name">
                    <col  width="65%" class="field-body">
                </colgroup>
                <tbody valign="top">
                <tr class="field-odd field">
                    <th class="field-name">Request parameters:</th>
                    <td class="field-body">
                        <ul class="first last simple">
                            {$requestParameters}
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
HTML;
        }

        if (empty($response)){
            $response = 'This call does not accept a response body.';
            $responseParametersHtml = '';
        } else {
            $responseParameters = $this->retrieveParametersAsHtml($response);
            $response = json_encode($response, JSON_PRETTY_PRINT);
            $responseParametersHtml = <<<HTML
            <table class="docutils field-list" frame="void" rules="none"  width="400">
                <colgroup>
                    <col width="35%" class="field-name">
                    <col  width="65%" class="field-body">
                </colgroup>
                <tbody valign="top">
                <tr class="field-odd field">
                    <th class="field-name">Response attributes:</th>
                    <td class="field-body">
                        <ul class="first last simple">
                            {$responseParameters}
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
HTML;
        }
        $resourcePath = urldecode($resourcePath);
        $resource = str_replace('/', '-', preg_replace('#/\w*/V\d+/(.*)#', '${1}', $resourcePath));
        $lowerCaseResource = strtolower($resource);
        $lowerCaseMethod = strtolower($httpMethod);
        $beginningHtml = <<<HTML
<div class="col-xs-9" role="main">
    <div class="bs-docs-section">
HTML;
        $headingHtml = <<<HTML
        <h2 class="api2" id="$lowerCaseResource">$resource</h2>
        <h3 class="api3" id="$lowerCaseMethod-$lowerCaseResource">$httpMethod $resourcePath</h3>
        <h4 class="api4">Request</h4>
HTML;
        $responseHtml = <<<HTML
        <h4 class="api4" id=”$lowerCaseResource-response>Response</h4>
HTML;
        $requestResponseParametersHtml = <<<HTML
        <h3 class="api3" id="$lowerCaseResource-parameters">Request and response parameters</h3>
HTML;
        $endHtml = $this->generateHtmlFooter();
        $content = "{$beginningHtml}\n{$headingHtml}\n<pre>\n{$arguments}\n</pre>\n{$responseHtml}\n<pre>\n{$response}"
        . "\n</pre>\n{$requestResponseParametersHtml}\n{$requestParametersHtml}\n{$responseParametersHtml}\n{$endHtml}";
        return $content;
    }

    /**
     * Generate the end html text;
     *
     * @return string
     */
    protected function generateHtmlFooter()
    {
        $endHtml = <<<HTML
        <h3 class="api3" id="products-responses">Response codes</h3>
        <table class="docutils field-list" frame="void" rules="none" width="400">
            <colgroup>
                <col  width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Normal response codes:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>SUCCESS_CODE</strong>SUCCESS_DESCRIPTION</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="docutils field-list" frame="void" rules="none" width="400">
            <colgroup>
                <col  width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Error response codes:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>ERROR_CODE</strong>ERROR_DESCRIPTION</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;
        return $endHtml;
    }

    /**
     * Generate a name of file
     *
     * @param $resourcePath
     * @return string
     * @throws \RuntimeException
     */
    protected function generateFileName()
    {
        $varDir = realpath(__DIR__ . '/../../../../../..') . '/var';
        $documentationDir = $varDir . '/log/rest-documentation/';
        $debugBackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $pathToFile = $documentationDir;
        foreach ($debugBackTrace as $traceItem) {
            /** Test invocation trace item is the only item which has 3 elements, other trace items have 5 elements */
            if (count($traceItem) == 3) {
                /** Remove 'test' prefix from method name, e.g. testCreate => create */
                $fileName = lcfirst(substr($traceItem['function'], 4));
                /** Remove 'Test' suffix from test class name */
                $pathToFile .= str_replace('\\', '/', substr($traceItem['class'], 0, -4)) . '/';
                break;
            }
        }
        if (!isset($fileName)) {
            $fileName = 'unclassified';
        }
        if (!file_exists($pathToFile)) {
            if (!mkdir($pathToFile, 0755, true)) {
                throw new \RuntimeException('Unable to create missing directory for REST documentation generation');
            }
        }
        $filePath = $pathToFile . $fileName . '.html';
        return $filePath;
    }

    /**
     * Retrieve parameters of response/request
     *
     * @param array|string $parameters
     * @return string
     */
    protected function retrieveParametersAsHtml($parameters)
    {
        $parametersAsHtml = '';
        if (is_array($parameters) && (!empty($parameters))) {
            foreach (array_keys($parameters) as $parameter) {
                $parametersAsHtml = $parametersAsHtml . '<li><strong>' . $parameter .
                    '</strong> (<em>Type should be changed manually!</em>) TBD.</li>' . "\n";
            }
        } else {
            $parametersAsHtml = '<li><strong>' . 'scalar_value' .
                '</strong> (<em>Type should be changed manually!</em>) TBD.</li>';
        }
        return $parametersAsHtml;
    }
}
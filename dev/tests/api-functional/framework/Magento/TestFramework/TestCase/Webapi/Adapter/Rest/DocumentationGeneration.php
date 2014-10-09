<?php
/**
 * Generator of documentation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\TestCase\Webapi\Adapter\Rest;

class DocumentationGeneration
{

    /**
     * Retrieve parameters of response/request
     *
     * @param array $parametersArray
     */
    protected function retrieveParameters($parametersArray)
    {
        if (is_array($parametersArray)) {
            $arguments = array_keys($parametersArray);
        } else {
            $arguments = $parametersArray;
        }
        $parameters = '';
        if (is_array($arguments)) {
            foreach ($arguments as $argument) {
                $parameters = $parameters . '<li><strong>' . $argument . '</strong> (<em>Type should be changed manually!</em>) TBD.</li>' . "\n";
            }
        } else {
            $parameters = '<li><strong>' . $parameters . '</strong> (<em>Type should be changed manually!</em>) TBD.</li>';
        }
        return $parameters;
    }

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
        $requestParameters = $this->retrieveParameters($arguments);
        $responseParameters = $this->retrieveParameters($response);
        $arguments = json_encode($arguments, JSON_PRETTY_PRINT);
        $response = json_encode($response, JSON_PRETTY_PRINT);
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
                $resource = preg_replace('#/\w*/V\d+/(.*)#', '${1}', $resourcePath);
                $resource = str_replace('/', '-', $resource);
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
        $resourcePath = urldecode($resourcePath);
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
        $requestParametersHtml = <<<HTML
        <h3 class="api3" id="$lowerCaseResource-parameters">Request and response parameters</h3>
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
                        <li><strong>200</strong> – Success.</li>
                        <li><strong>201</strong> – Success.</li>
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
                        <li><strong>404</strong> – Something went wrong.</li>
                        <li><strong>501</strong> – Something else went wrong.</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace($endHtml, '', $fileContent);
            $content = "{$fileContent}\n{$headingHtml}\n<pre>\n{$arguments}\n</pre>\n{$responseHtml}\n<pre>\n{$response}\n</pre>\n{$requestParametersHtml}\n{$responseParametersHtml}\n{$endHtml}";
            unlink($filePath);
            file_put_contents($filePath, $content, FILE_APPEND);
        } else {
            if ($resourcePath && $arguments && $response) {
                if (!is_writable(dirname($filePath))) {
                    throw new \RuntimeException('Directory for documentation generation is not writable.');
                }
                $content = "{$beginningHtml}\n{$headingHtml}\n<pre>\n{$arguments}\n</pre>\n{$responseHtml}\n<pre>\n{$response}\n</pre>\n{$requestParametersHtml}\n{$responseParametersHtml}\n{$endHtml}";
                file_put_contents($filePath, $content, FILE_APPEND);
            }
        }
    }
}
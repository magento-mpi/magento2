<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Authentication\Rest\OauthClient;

use OAuth\Common\Http\Uri\UriInterface;

require_once __DIR__ . '/../../../../../../lib/OAuth/bootstrap.php';

/**
 * Signature class for Magento REST API.
 */
class Signature extends \OAuth\OAuth1\Signature\Signature
{
    /**
     * {@inheritdoc}
     *
     * In addition to the original method, allows array parameters for filters.
     */
    public function getSignature(UriInterface $uri, array $params, $method = 'POST')
    {
        parse_str($uri->getQuery(), $queryStringData);

        $allParams = array_merge($queryStringData, $params);
        ksort($allParams);

        foreach ($allParams as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $signatureData[] = [
                        'key' => rawurlencode("{$key}[{$subKey}]"),
                        'value' => rawurlencode($subValue)
                    ];
                }
            } else {
                $signatureData[] = ['key' => rawurlencode($key), 'value' => rawurlencode($value)];
            }
        }
        // determine base uri
        $baseUri = $uri->getScheme() . '://' . $uri->getRawAuthority();

        if ('/' == $uri->getPath()) {
            $baseUri.= $uri->hasExplicitTrailingHostSlash() ? '/' : '';
        } else {
            $baseUri .= $uri->getPath();
        }

        $baseString = strtoupper($method) . '&';
        $baseString.= rawurlencode($baseUri) . '&';
        $baseString.= rawurlencode($this->buildSignatureDataString($signatureData));

        return base64_encode($this->hash($baseString));
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSignatureDataString(array $signatureData)
    {
        $signatureString = '';
        $delimiter = '';
        foreach ($signatureData as $dataItem) {
            $signatureString .= $delimiter . $dataItem['key'] . '=' . $dataItem['value'];

            $delimiter = '&';
        }

        return $signatureString;
    }
}

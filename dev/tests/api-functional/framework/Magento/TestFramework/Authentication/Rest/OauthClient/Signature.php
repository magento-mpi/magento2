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
        foreach ($allParams as $key => $value) {
            if (is_array($value)) {
                /** Implementation for complex filters parameters */
                foreach ($value as $filterIndex => $filterMeta) {
                    foreach ($filterMeta as $filterMetaKey => $filterMetaValue) {
                        $signatureData[] = [
                            'key' => rawurlencode("{$key}[{$filterIndex}][{$filterMetaKey}]"),
                            'value' => rawurlencode($filterMetaValue)
                        ];
                    }
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
        usort(
            $signatureData,
            function ($a, $b) {
                if ($a['key'] == $b['key']) {
                    return 0;
                } elseif ($a['key'] > $b['key']) {
                    return 1;
                } else {
                    return -1;
                }
            }
        );
        $delimiter = '';
        foreach ($signatureData as $dataItem) {
            $signatureString .= $delimiter . $dataItem['key'] . '=' . $dataItem['value'];

            $delimiter = '&';
        }

        return $signatureString;
    }
}

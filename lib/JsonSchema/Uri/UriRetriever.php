<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Uri;

use JsonSchema\Uri\Retrievers\FileGetContents;
use JsonSchema\Uri\Retrievers\UriRetrieverInterface;
use JsonSchema\Validator;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\UriResolverException;

/**
 * Retrieves JSON Schema URIs
 *
 * @author Tyler Akins <fidian@rumkin.com>
 */
class UriRetriever
{
    protected $uriRetriever = null;

    /**
     * Guarantee the correct media type was encountered
     *
     * @throws InvalidSchemaMediaTypeException
     */
    public function confirmMediaType($uriRetriever)
    {
        $contentType = $uriRetriever->getContentType();

        if (is_null($contentType)) {
            // Well, we didn't get an invalid one
            return;
        }

        if (Validator::SCHEMA_MEDIA_TYPE === $contentType) {
            return;
        }

        throw new InvalidSchemaMediaTypeException(sprintf('Media type %s expected', Validator::SCHEMA_MEDIA_TYPE));
    }

    /**
     * Get a URI Retriever
     *
     * If none is specified, sets a default FileGetContents retriever and
     * returns that object.
     *
     * @return UriRetrieverInterface
     */
    public function getUriRetriever()
    {
        if (is_null($this->uriRetriever)) {
            $this->setUriRetriever(new FileGetContents);
        }

        return $this->uriRetriever;
    }

    /**
     * Resolve a schema based on pointer
     *
     * URIs can have a fragment at the end in the format of
     * #/path/to/object and we are to look up the 'path' property of
     * the first object then the 'to' and 'object' properties.
     *
     * @param object $jsonSchema JSON Schema contents
     * @param string $uri JSON Schema URI
     * @return object JSON Schema after walking down the fragment pieces
     */
    public function resolvePointer($jsonSchema, $uri)
    {
        $resolver = new UriResolver();
        $parsed = $resolver->parse($uri);
        if (empty($parsed['fragment'])) {
            return $jsonSchema;
        }

        $path = explode('/', $parsed['fragment']);
        while ($path) {
            $pathElement = array_shift($path);
            if (! empty($pathElement)) {
                $pathElement = str_replace('~1', '/', $pathElement);
                $pathElement = str_replace('~0', '~', $pathElement);
                if (! empty($jsonSchema->$pathElement)) {
                    $jsonSchema = $jsonSchema->$pathElement;
                } else {
                    $jsonSchema = new \stdClass();
                }

                if (! is_object($jsonSchema)) {
                    $jsonSchema = new \stdClass();
                }
            }
        }

        return $jsonSchema;
    }

    /**
     * Retrieve a URI
     *
     * @param string $uri JSON Schema URI
     * @param null $baseUri
     * @return object
     * @throws \JsonSchema\Exception\JsonDecodingException
     */
    public function retrieve($uri, $baseUri = null)
    {
        $resolver = new UriResolver();
        $resolvedUri = $resolver->resolve($uri, $baseUri);
        $uriRetriever = $this->getUriRetriever();
        $contents = $this->uriRetriever->retrieve($resolvedUri);
        $this->confirmMediaType($uriRetriever);
        $jsonSchema = json_decode($contents);

        if (JSON_ERROR_NONE < $error = json_last_error()) {
            throw new JsonDecodingException($error);
        }

        // Use the JSON pointer if specified
        $jsonSchema = $this->resolvePointer($jsonSchema, $resolvedUri);
        $jsonSchema->id = $resolvedUri;

        return $jsonSchema;
    }

    /**
     * Set the URI Retriever
     *
     * @param UriRetrieverInterface $uriRetriever
     * @return $this for chaining
     */
    public function setUriRetriever(UriRetrieverInterface $uriRetriever)
    {
        $this->uriRetriever = $uriRetriever;

        return $this;
    }

    /**
     * Parses a URI into five main components
     *
     * @param string $uri
     * @return array
     */
    public function parse($uri)
    {
        preg_match('|^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?|', $uri, $match);

        $components = array();
        if (5 < count($match)) {
            $components =  array(
                'scheme'    => $match[2],
                'authority' => $match[4],
                'path'      => $match[5]
            );
        }

        if (7 < count($match)) {
            $components['query'] = $match[7];
        }

        if (9 < count($match)) {
            $components['fragment'] = $match[9];
        }

        return $components;
    }

    /**
     * Builds a URI based on n array with the main components
     *
     * @param array $components
     * @return string
     */
    public function generate(array $components)
    {
        $uri = $components['scheme'] . '://'
             . $components['authority']
             . $components['path'];

        if (array_key_exists('query', $components)) {
            $uri .= $components['query'];
        }

        if (array_key_exists('fragment', $components)) {
            $uri .= $components['fragment'];
        }

        return $uri;
    }

    /**
     * Resolves a URI
     *
     * @param string $uri Absolute or relative
     * @param type $baseUri Optional base URI
     * @return string
     */
    public function resolve($uri, $baseUri = null)
    {
        $components = $this->parse($uri);
        $path = $components['path'];

        if ((array_key_exists('scheme', $components)) && ('http' === $components['scheme'])) {
            return $uri;
        }

        $baseComponents = $this->parse($baseUri);
        $basePath = $baseComponents['path'];

        $baseComponents['path'] = self::combineRelativePathWithBasePath($path, $basePath);

        return $this->generate($baseComponents);
    }

    /**
     * Tries to glue a relative path onto an absolute one
     *
     * @param string $relativePath
     * @param string $basePath
     * @return string Merged path
     * @throws UriResolverException
     */
    private static function combineRelativePathWithBasePath($relativePath, $basePath)
    {
        $relativePath = self::normalizePath($relativePath);
        $basePathSegments = self::getPathSegments($basePath);

        preg_match('|^/?(\.\./(?:\./)*)*|', $relativePath, $match);
        $numLevelUp = floor(strlen($match[0]) / 3 + 1);
        if ($numLevelUp > count($basePathSegments)) {
            throw new UriResolverException(sprintf("Unable to resolve URI '%s' from base '%s'", $relativePath, $basePath));
        }

        $basePathSegments = array_slice($basePathSegments, 0, -$numLevelUp);
        $path = preg_replace('|^/?(\.\./(\./)*)*|', '', $relativePath);

        return implode(DIRECTORY_SEPARATOR, $basePathSegments) . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Normalizes a URI path component by removing dot-slash and double slashes
     *
     * @param string $path
     * @return string
     */
    private static function normalizePath($path)
    {
        $path = preg_replace('|((?<!\.)\./)*|', '', $path);
        $path = preg_replace('|//|', '/', $path);

        return $path;
    }

    /**
     * @param string $path
     * @return array
     */
    private static function getPathSegments($path)
    {
        return explode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param string $uri
     * @return boolean
     */
    public function isValid($uri)
    {
        $components = $this->parse($uri);

        return !empty($components);
    }
}

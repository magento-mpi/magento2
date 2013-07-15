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
}

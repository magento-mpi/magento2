<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

class ResponseConverter
{
    /**
     * Converting data from raw data after execution fetchAssoc to data for DocumentFactory
     *
     * @param array $raw Data after execution fetchAssoc
     * @return array
     */
    public function convertToDocument(array $raw)
    {
        $documentList = [];
        foreach ($raw as $document) {
            $documentFieldList = [];
            foreach ($document as $name => $values) {
                $documentFieldList[] = [
                    'name' => $name,
                    'values' => $values
                ];
            }
            $documentList[] = $documentFieldList;
        }
        return $documentList;
    }
}

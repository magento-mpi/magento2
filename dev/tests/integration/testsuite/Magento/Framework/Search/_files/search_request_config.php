<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    "suggested_search_container" => [
        "dimensions" => [
            "scope" => [
                "name" => "scope",
                "value" => "default"
            ]
        ],
        "queries" => [
            "suggested_search_container" => [
                "name" => "suggested_search_container",
                "boost" => "2",
                "queryReference" => [
                    [
                        "clause" => "must",
                        "ref" => "fulltext_search_query"
                    ],
                    [
                        "clause" => "should",
                        "ref" => "fulltext_search_query2"
                    ]
                ],
                "type" => "boolQuery"
            ],
            "fulltext_search_query" => [
                "name" => "fulltext_search_query",
                "boost" => "5",
                "match" => [
                    [
                        "field" => "title",
                        "value" => "\$request.title",
                        "boost" => "2"
                    ],
                    [
                        "field" => "description",
                        "value" => "%request.description%"
                    ]
                ],
                "type" => "matchQuery"
            ],
            "fulltext_search_query2" => [
                "name" => "fulltext_search_query2",
                "filterReference" => [
                    [
                        "ref" => "promoted"
                    ]
                ],
                "type" => "filteredQuery"
            ]
        ],
        "filters" => [
            "promoted" => [
                "name" => "promoted",
                "filterReference" => [
                    [
                        "clause" => "must",
                        "ref" => "price_name"
                    ],
                    [
                        "clause" => "should",
                        "ref" => "price_name1"
                    ]
                ],
                "type" => "boolFilter"
            ],
            "price_name" => [
                "field" => "promoted_boost",
                "name" => "price_name",
                "from" => "10",
                "to" => "100",
                "type" => "rangeFilter"
            ],
            "price_name1" => [
                "name" => "price_name1",
                "field" => "price_name",
                "value" => "\$name",
                "type" => "termFilter"
            ]
        ],
        "from" => "10",
        "size" => "10",
        "query" => "suggested_search_container",
        "index" => "product"
    ]
];
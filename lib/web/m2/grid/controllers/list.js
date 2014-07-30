define([
	'./module'
], function(controllers){

    controllers.controller('PhoneListCtrl', ['$scope', '$sce', function($scope, $sce){
        $scope.rows = [
            {   
                "class": "even",
                rowData: [
                    {
                        "class": "col-select col-massaction",
                        "content": '<input type="checkbox" name="customer" value="1" class="massaction-checkbox">'
                    },
                    {
                        "class": "col-entity_id col-number",
                        "content": "1"
                    },
                    {
                        "class": "col-name",
                        "content": 'Denis Rul'
                    },
                    {
                        "class": "col-email",
                        "content": 'drul@ebay.com'
                    },
                    {
                        "class": "col-group",
                        "content": 'General'
                    },
                    {
                        "class": "col-phone col-billing_telephone",
                        "content": '&nbsp;'
                    },
                    {
                        "class": "col-billing_postcode",
                        "content": '&nbsp;'
                    },
                    {
                        "class": "col-billing_country_id",
                        "content": '&nbsp;'
                    },
                    {
                        "class": "col-billing_region",
                        "content": '&nbsp;'
                    },
                    {
                        "class": "col-customer_since",
                        "content": 'Jun 20, 2014 5:53:20 AM'
                    },
                    {
                        "class": "col-store col-website_id",
                        "content": 'Main Website'
                    },
                    {
                        "class": "col-actions col-action last",
                        "content": '<a>Edit</a>'
                    }
                ]
            }
        ];

        $scope.headings = [
            {
                "class": "col-select col-massaction",
                "content": ""
            },
            {
                "class": "col-entity_id",
                "content": "ID"
            },
            {
                "class": "col-name",
                "content": "Name"
            },
            {
                "class": "col-email",
                "content": "Email"
            },
            {
                "class": "col-group",
                "content": "Group"
            },
            {
                "class": "col-phone col-billing_telephone",
                "content": "Phone"
            },
            {
                "class": "col-billing_postcode",
                "content": "ZIP"
            },
            {
                "class": "col-billing_country_id",
                "content": "Country"
            },
            {
                "class": "col-billing_region",
                "content": "State/Province"
            },
            {
                "class": "col-customer_since",
                "content": "Customer Since"
            },
            {
                "class": "col-store col-website_id",
                "content": "Web Site"
            },
            {
                "class": "col-actions last no-link col-action",
                "content": "Action"
            }
        ]


        $scope.toTrusted = function(html){
            return $sce.trustAsHtml(html);
        }
    }]);
});
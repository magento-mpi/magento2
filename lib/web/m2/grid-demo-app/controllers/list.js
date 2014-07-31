define([
    'angular',
	'./module'
], function(ng, controllers){

    controllers.controller('PhoneListCtrl', ['$scope', '$sce', 'config',
        function( $scope, $sce, config ){
            ng.extend($scope, config);
            
            $scope.toTrusted = function(html){
                return $sce.trustAsHtml(html);
            };
        }
    ]);
});
function eventPerformerController($scope, $http, $window){
  $scope.removePerformer = function(eventId, performerId){
    var path = "admin/event/"+ eventId +"/performer/"+ performerId + "/remove";
    $http.post("api.php?path="+path)
	.success(function success(){ $window.location.reload(); })
	.error(function error(data, status){
        	alert("Failed with " + status + " " + data.error);
      	});
  };

  $scope.deletePerformer = function(pId){
    alert("delete " + pId);
  }
}

var adminApp = angular.module("adminApp", []);
adminApp.controller('eventPerformerCtr', eventPerformerController);
Directives.directive('ngConfirmClick', [
    function(){
        return {
            priority: -1,
            restrict: 'A',
            link: function(scope, element, attrs){
                element.bind('click', function(e){
                    var message = attrs.ngConfirmClick;
                    if(message && !confirm(message)){
                        e.stopImmediatePropagation();
                        e.preventDefault();
                    }
                });
            }
        }
    }
]);
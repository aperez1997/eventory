function eventPerformerController($scope, $http, $window){
  var reload = function(){
    $window.location.reload();
  };
  var error = function error(data, status){
    alert("Failed with " + status + " " + data.error);
  };

  $scope.removePerformer = function(eventId, performerId){
    var path = "admin/event/"+ eventId +"/performer/"+ performerId + "/remove";
    $http.post("api.php?path="+path)
	.success(reload)
	.error(error);
  };

  $scope.deletePerformer = function(pId){
    var path = "admin/performer/"+ pId + "/delete";
    $http.post("api.php?path="+path)
        .success(reload)
        .error(error);
  }
}

var adminApp = angular.module("adminApp", []);
adminApp.controller('eventPerformerCtr', eventPerformerController);
adminApp.directive('ngConfirmClick', function(){
  return {
    priority: -1,
    restrict: 'A',
    replace: false,
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
});

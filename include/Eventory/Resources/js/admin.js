function eventPerformerController($scope, $http, $window){
  $scope.removePerformer = function(eventId, performerId){
    var path = "admin/event/"+ eventId +"/performer/"+ performerId + "/remove";
    $http.post("/api.php?path="+path).then(
      function success(){ $window.location.reload(); },
      function error(data, status){
        alert("Failed with " + status + " " + data.error);
      }
    );
  };
}

var adminApp = angular.module("adminApp", []);
adminApp.controller('eventPerformerCtr', eventPerformerController);
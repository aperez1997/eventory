var browseApp = angular.module('browsePerformersApp', []);
browseApp.controller('browsePerformersCtr', browsePerformersController);

function browsePerformersController($scope, $http){
  $scope.predicate = 'sort_default';
  $scope.reverse = false;
  $scope.performerList = [];
  $http.get('/api.php?path=/performer/browse').then(function(data){
    $scope.performerList = data.performers;
  });
}
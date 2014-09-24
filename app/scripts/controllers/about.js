'use strict';

/**
 * @ngdoc function
 * @name iconApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the iconApp
 */
angular.module('iconApp')
  .controller('AboutCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

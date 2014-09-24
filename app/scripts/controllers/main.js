'use strict';

/**
 * @ngdoc function
 * @name iconApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the iconApp
 */
angular.module('iconApp')
  .controller('MainCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

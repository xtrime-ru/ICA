import Vue from 'vue'
var NumAbbr = require('number-abbreviate')
var numAbbr = new NumAbbr(['K', 'M', 'B', 'T'])

Vue.filter('abbreviate', function (value) {
  return numAbbr.abbreviate(value, 0)
})
Dropzone.autoDiscover = false;
var app = angular.module('dropzone', []);
app.directive('dropzone', function() {
	return function(scope, element, attrs) {
		element.dropzone({
			url: 'king-include/newsupload.php',
			thumbnailHeight: 160,
			thumbnailWidth: 160,
			addRemoveLinks: true,
			maxFiles: 1,
			acceptedFiles: 'image/jpeg,image/png,image/gif',
			dictRemoveFile: '',
			dictCancelUpload: '<i class="far fa-stop-circle"></i>',
			previewTemplate: '<div class="dz-preview dz-file-preview"><img data-dz-thumbnail /><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div>',
			init: function() {
				this.on('success', function(file, response) {
					var e = JSON.parse(response);
					scope.$apply(function() {
						
						scope.input.img = e.id;
					});
					var removeButton = Dropzone.createElement('<a class="d-remove"><i class="fas fa-trash-alt"></i></a>');
					var _this = this;
					removeButton.addEventListener("click", function(a) {
						a.preventDefault();
						a.stopPropagation();
						_this.removeFile(file);
						$.ajax({
							type: 'POST',
							url: 'king-include/multipledelete.php',
							data: {
								'fileid': e.id,
								'thumbid': ''
							},
							success: function(data) {
								file.previewElement.remove();
								scope.$apply(function() {
									scope.input.img = '';
								});
							}
						});
					});
					file.previewElement.appendChild(removeButton);
				});
			}
		});
	}
});
angular.module('plunker', ['ui.sortable', 'dropzone']);
angular.module('plunker').controller('MyCtrl', ['$scope', function($scope) {
	$scope.sortableOptions = {
		handle: 'div .listhandle',
		axis: 'y'
	}
	$scope.sortableOptions2 = {
		handle: 'div .gridhandle'
	}
	$scope.inputs = [{
		choices: '',
		tabz: 'grid1'
	}];

	$scope.addInput = function() {
		$scope.inputs.push({tabz: 'grid1'});
	}

	$scope.removeInput = function(index) {
		$scope.inputs.splice(index, 1);
	}
}]);
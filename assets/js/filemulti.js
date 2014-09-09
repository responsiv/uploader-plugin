/*
 * FileMulti class
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";

    var FileMulti = function (el, options) {

        // Main element
        this.$el = $(el)
        this.options = options

        // Init
        this.init()
    }

    FileMulti.prototype.handleReady = function() {
        this.$statusReady.show()
        this.$statusLoading.hide()
        this.$statusError.hide()
    }

    FileMulti.prototype.handleLoading = function() {
        var $bar = $('.progress-bar', self.$progressBar)
        $bar.css('width', '0%')

        this.$statusLoading.show()
        this.$statusReady.hide()
        this.$statusError.hide()
    }

    FileMulti.prototype.handleError = function(file, index) {
        this.$statusReady.hide()
        this.$statusLoading.hide()
        this.$statusError.show()
        if (file.error) this.$statusError.text(error)
    }

    FileMulti.prototype.handleCancelButton = function(e) {
        e.preventDefault()

        var template = $(e.currentTarget).closest('.rv-file-template'),
            data = template.data('data') || {}

        data.context = data.context || template
        if (data.abort) {
            data.abort()
        } else {
            template.remove()
        }
    }

    FileMulti.prototype.init = function() {

        var self = this

        var formData = {
            'X_RESPONSIV_UPLOADER_UPLOAD': 1,
            '_handler': this.options.handler
        }

        var uploaderOptions = {
            // url: url,
            dropZone: this.$dropZone,
            dataType: 'json',
            autoUpload: this.options.autoUpload,
            // acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: (this.options.maxSize * 1000000), // 5 MB
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            previewMaxWidth: this.options.previewWidth,
            previewMaxHeight: this.options.previewHeight,
            previewCrop: true,
            formData: formData,
            paramName: 'files'
        }

        if (this.options.fileTypes && this.options.fileTypes != '*')
            uploaderOptions.acceptFileTypes = new RegExp("(\.|\/)("+this.options.fileTypes+")$", 'i')

        // Shared properties
        this.$uploadBtn = $('.rv-upload-btn:first', this.$el)
        this.$uploadInput = $('.rv-upload-input:first', this.$el).hide()
        this.$uploadFiles = $('.rv-upload-files:first', this.$el)
        this.$dropZone = $('.rv-upload-dropzone:first', this.$el)
        this.$progressBar = $('.rv-upload-progress:first', this.$el)
        this.uploadTemplate = $('.rv-upload-template:first', this.$el).html()
        this.downloadTemplate = $('.rv-download-template:first', this.$el).html()

        this.$statusReady = $('.status.ready', this.$dropZone)
        this.$statusLoading = $('.status.loading', this.$dropZone).hide()
        this.$statusError = $('.status.error', this.$dropZone).hide()

        this.$uploadBtn.on('click', function(){
            self.$uploadInput = $('.rv-upload-input:first', this.$el)
            self.$uploadInput.val('').trigger('click')
        })

        this.$uploadFiles.on('click', '.cancel', this.handleCancelButton)

        this.$uploadInput
            .fileupload(uploaderOptions)
            .on('fileuploadsend', function (e, data) {
                self.handleLoading()
            })
            .on('fileuploadadd', function (e, data) {

                // console.log(Mustache.render(self.uploadTemplate))
                data.context = $('<tbody />').appendTo(self.$uploadFiles);

                $.each(data.files, function (index, file) {

                    file.sizeHuman = self.formatFileSize(file.size)

                    data.context.append(Mustache.render(self.uploadTemplate, {
                        file: file
                    }))

                    // var node = $(Mustache.render(self.uploadTemplate))

                    // var node = $('<p/>')
                    //         .append($('<span/>').text(file.name));
                    // if (!index) {
                    //     node
                    //         .append('<br>')
                    //         .append(uploadButton.clone(true).data(data));
                    // }
                    // node.appendTo(data.context);
                });
            })
            .on('fileuploadprocessalways', function (e, data) {
                var index = data.index,
                    file = data.files[index],
                    node = $(data.context.children()[index]),
                    $preview = $('.preview:first', node)

                if (file.preview) {
                    $preview.append(file.preview)
                }

                // Error
                if (file.error) {
                    self.handleError(file)
                }
                // Last
                if (index + 1 === data.files.length) {
                    // data.context.find('button')
                    //     .text('Upload')
                    //     .prop('disabled', !!data.files.error);
                }
            })
            .on('fileuploadprogressall', function (e, data) {
                var $bar = $('.progress-bar', self.$progressBar),
                    progress = parseInt(data.loaded / data.total * 100, 10)

                $bar.css('width', progress + '%')
            })
            .on('fileuploaddone', function (e, data) {
                $.each(data.result.files, function (index, file) {
                    if (file.url) {
                        var newContainer = Mustache.render(self.downloadTemplate, {
                            file: file
                        })

                        $(data.context.children()[index]).replaceWith(newContainer)
                    }
                    // Error
                    else if (file.error) {
                        self.handleError(file)
                        // var error = $('<span class="text-danger"/>').text(file.error);
                        // $(data.context.children()[index])
                        //     .append('<br>')
                        //     .append(error);
                    }
                });
            })
            .on('fileuploadfail', function (e, data) {
                $.each(data.files, function (index, file) {
                    self.handleError(file, index)
                    // var error = $('<span class="text-danger"/>').text('File upload failed.');
                    // $(data.context.children()[index])
                    //     .append('<br>')
                    //     .append(error);
                });
            })

    }

    FileMulti.prototype.formatFileSize = function(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }
        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }
        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }
        return (bytes / 1000).toFixed(2) + ' KB';
    }

    window.$rvFileMulti = FileMulti;

}(window.jQuery);




$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = window.location


        // uploadButton = $('<button/>')
        //     .addClass('btn btn-primary')
        //     .prop('disabled', true)
        //     .text('Processing...')
        //     .on('click', function () {
        //         var $this = $(this),
        //             data = $this.data();
        //         $this
        //             .off('click')
        //             .text('Abort')
        //             .on('click', function () {
        //                 $this.remove();
        //                 data.abort();
        //             });
        //         data.submit().always(function () {
        //             $this.remove();
        //         });
        //     });

})
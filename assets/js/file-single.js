/*
 * FileSingle class
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";

    var FileSingle = function (el, options) {

        // Main element
        this.$el = $(el)
        this.options = options

        // Init
        this.init()
    }

    FileSingle.prototype.init = function() {

        var self = this

        this.$uploadButton = this.$el

        this.dropzone = new Dropzone(this.$uploadButton.closest('form').get(0), {
            url: window.location,
            clickable: this.$uploadButton.get(0),
            acceptedFiles: '.zip',
            paramName: 'uploaded_file'
        })

        var self = this

        this.dropzone.on("error", function(file, error){
            self.$uploadButton.removeClass('loading').removeAttr('disabled')
            alert(error)
        })
        this.dropzone.on("sending", function(file, xhr, formData) {
            self.$uploadButton.addClass('loading').attr('disabled', 'disabled')
        })
        this.dropzone.on("success", function(file, data){
            window.location = data
        })

    }

    window.$rvFileSingle = FileSingle;

}(window.jQuery);

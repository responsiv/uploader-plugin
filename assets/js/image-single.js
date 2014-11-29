/*
 * ImageSingle class
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";
    var SingleImageUploader = function (element, options) {
        this.options = options

        this.$container = $(element)
        this.$el = $('.content', element)
        this.$imageHolder = $('div.img', this.$el)

        this.$container.append('<div class="loading">')

        this.dropzone = new Dropzone(this.$container.get(0), {
            url: window.location,
            clickable: this.$el.get(0),
            acceptedFiles: this.options.extensions,
            paramName: 'file_data'
        })

        var self = this

        this.dropzone.on("error", function(file, error){
            alert(error)
            self.stop()
        })
        this.dropzone.on("sending", function(file, xhr, formData) {
            self.start()
        })
        this.dropzone.on("success", function(file, error){
            self.updateImage()
        })
    }

    SingleImageUploader.prototype.updateImage = function() {
        var updateList = {}
        updateList[this.options.updatePartial] = this.$el

        var request = this.$container.closest('form').request(this.options.handler, {
               'update': updateList
            }),
            self = this

        request.success(function() {
            self.stop()
        })
    }

    SingleImageUploader.prototype.start = function() {
        this.$container.addClass('loading')
    }

    SingleImageUploader.prototype.stop = function() {
        this.$container.removeClass('loading')
    }

    SingleImageUploader.DEFAULTS = {
        handler: 'onUpdateImage',
        extensions: '.gif,.jpg,.jpeg,.png,.gif',
        updatePartial: 'uploader/image-single'
    }

    var old = $.fn.singleImageUploader

    $.fn.singleImageUploader = function (option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('oc.singleImageUploader')
            var options = $.extend({}, SingleImageUploader.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data) $this.data('oc.singleImageUploader', (data = new SingleImageUploader(this, options)))
            if (typeof option == 'string') data[option].call($this)
        })
      }

    $.fn.singleImageUploader.Constructor = SingleImageUploader

    $.fn.singleImageUploader.noConflict = function () {
        $.fn.singleImageUploader = old
        return this
    }

    // ICON UPLOAD CONTROL DATA-API
    // ===============
    $(document).ready(function(){
        $('[data-control=single-image-uploader]').singleImageUploader()
    })

}(window.jQuery);
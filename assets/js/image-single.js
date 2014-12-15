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

        var acceptedFiles = this.options.fileTypes
        if (acceptedFiles == '*')
            acceptedFiles = null

        this.dropzone = new Dropzone(this.$container.get(0), {
            url: window.location,
            clickable: this.$el.get(0),
            acceptedFiles: acceptedFiles,
            paramName: 'file_data'
        })

        var self = this

        this.dropzone.on("error", function(file, error){
            alert(error)
            self.stop()
        })
        this.dropzone.on("sending", function(file, xhr, formData) {
            self.addExtraFormData(formData)
            self.start()
        })
        this.dropzone.on("success", function(file, error){
            self.updateImage()
        })
    }

    SingleImageUploader.prototype.addExtraFormData = function(formData) {
        if (this.options.extraData) {
            $.each(this.options.extraData, function (name, value) {
                formData.append(name, value)
            })
        }

        var $form = this.$el.closest('form')
        if ($form.length > 0) {
            $.each($form.serializeArray(), function (index, field) {
                formData.append(field.name, field.value)
            })
        }
    }

    SingleImageUploader.prototype.updateImage = function() {
        var updateList = {}
        updateList[this.options.updatePartial] = this.$el

        var request = this.$container.closest('form').request(this.options.handler, {
                update: updateList,
                redirect: false
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
        maxSize: null,
        fileTypes: '.gif,.jpg,.jpeg,.png',
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
    $(document).render(function(){
        $('[data-control=single-image-uploader]').singleImageUploader()
    })

}(window.jQuery);
/*
 * ImageMulti class
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";
    var MultiImageUploader = function (element, options) {
        this.options = options

        this.$container = $(element)
        this.$el = $('.content', element)
        this.$clickable = $('.clickable', element)
        this.$template = $('.template', element)
        this.$imageHolder = $('div.img', this.$el)

        var acceptedFiles = this.options.fileTypes
        if (acceptedFiles == '*')
            acceptedFiles = null

        this.dropzone = new Dropzone(this.$container.get(0), {
            url: window.location,
            clickable: this.$clickable.get(0),
            acceptedFiles: acceptedFiles,
            previewsContainer: this.$el.get(0),
            previewTemplate: this.$template.html(),
            paramName: 'file_data'
        })

        var self = this

        this.dropzone.on("error", function(file, error){
            alert(error)
        })

        this.dropzone.on("sending", function(file, xhr, formData) {
            self.addExtraFormData(formData)
        })

        this.dropzone.on("success", function(file, response){
            self.updateImage(file, response)
        })

        this.$el.on('click', '.delete', function(){
            self.removeImage($(this))
        })
    }

    // file.previewElement

    MultiImageUploader.prototype.addExtraFormData = function(formData) {
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

    MultiImageUploader.prototype.updateImage = function(file, response) {
        var $preview = $(file.previewElement),
            $img = $('.thumbnail', $preview)

        if (response.id) {
            $preview.data('id', response.id)
            $img.attr('src', response.thumb)
        }
    }

    MultiImageUploader.prototype.removeImage = function($link) {

        var self = this,
            $preview = $link.closest('.dz-preview'),
            id = $preview.data('id')

        if (!id) {
            $preview.remove()
            return
        }

        $preview.removeClass('dz-success').addClass('dz-removing')

        var request = this.$container.closest('form').request(this.options.handler, {
            data: {
                id: id,
                mode: 'delete'
            },
            redirect: false
        })

        request.success(function() {
            $preview.remove()
        })
    }

    MultiImageUploader.DEFAULTS = {
        handler: 'onUpdateImage',
        maxSize: null,
        fileTypes: '.gif,.jpg,.jpeg,.png'
    }

    var old = $.fn.multiImageUploader

    $.fn.multiImageUploader = function (option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('oc.multiImageUploader')
            var options = $.extend({}, MultiImageUploader.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data) $this.data('oc.multiImageUploader', (data = new MultiImageUploader(this, options)))
            if (typeof option == 'string') data[option].call($this)
        })
      }

    $.fn.multiImageUploader.Constructor = MultiImageUploader

    $.fn.multiImageUploader.noConflict = function () {
        $.fn.multiImageUploader = old
        return this
    }

    // ICON UPLOAD CONTROL DATA-API
    // ===============
    $(document).render(function(){
        $('[data-control=multi-image-uploader]').multiImageUploader()
    })

}(window.jQuery);
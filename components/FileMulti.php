<?php namespace Responsiv\Uploader\Components;

use Input;
use Cms\Classes\ComponentBase;
use Cms\Classes\CombineAssets;

class FileMulti extends ComponentBase
{

    /**
     * Supported file types.
     * @var array
     */
    public $fileTypes;

    /**
     * Unique API code for this component instance.
     * @var string
     */
    public $apiCode;

    public function componentDetails()
    {
        return [
            'name'        => 'File (Multi)',
            'description' => 'Upload multiple files'
        ];
    }

    public function defineProperties()
    {
        return [
            'apiCode' => [
                'title'       => 'API Code',
                'description' => 'A unique API code for this component so it can be referenced by others.',
                'default'     => 'uploader',
                'type'        => 'string',
            ],
            'autoUpload' => [
                'title'       => 'Automatically upload files',
                'description' => 'If checked, files will upload as soon as they are selected.',
                'type'        => 'checkbox',
            ],
            'maxSize' => [
                'title'       => 'Max file size (MB)',
                'description' => 'The maximum file size that can be uploaded in megabytes.',
                'default'     => '5',
                'type'        => 'string',
            ],
            'fileTypes' => [
                'title'       => 'Supported file types',
                'description' => 'File extensions separated by commas (,) or star (*) to allow all types.',
                'default'     => '*',
                'type'        => 'string',
            ],
            'previewWidth' => [
                'title'       => 'Image preview width',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default'     => '100',
                'type'        => 'string',
            ],
            'previewHeight' => [
                'title'       => 'Image preview height',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default'     => '100',
                'type'        => 'string',
            ],
        ];
    }

    public function init()
    {
        $this->fileTypes = $fileTypes = $this->property('fileTypes', '*');
        if ($fileTypes != '*')
            $this->fileTypes = array_map('trim', explode(',', $fileTypes));

        $this->apiCode = $this->property('apiCode', 'uploader');
    }

    public function onRun()
    {
        $assets = [
            'assets/vendor/mustache/mustache.js',
            'assets/js/vendor/jquery.ui.widget.js',
            'assets/js/vendor/canvas-to-blob.js',
            'assets/vendor/load-image/js/load-image.js',
            'assets/vendor/load-image/js/load-image-ios.js',
            'assets/vendor/load-image/js/load-image-orientation.js',
            'assets/vendor/load-image/js/load-image-meta.js',
            'assets/vendor/load-image/js/load-image-exif.js',
            'assets/vendor/load-image/js/load-image-exif-map.js',
            'assets/vendor/file-upload/js/jquery.iframe-transport.js',
            'assets/vendor/file-upload/js/jquery.fileupload.js',
            'assets/vendor/file-upload/js/jquery.fileupload-process.js',
            'assets/vendor/file-upload/js/jquery.fileupload-image.js',
            'assets/vendor/file-upload/js/jquery.fileupload-audio.js',
            'assets/vendor/file-upload/js/jquery.fileupload-video.js',
            'assets/vendor/file-upload/js/jquery.fileupload-validate.js',
        ];

        $assets[] = 'assets/js/filemulti.js';

        $this->addJs(CombineAssets::combine($assets, $this->assetPath));
    }

    public function onUpload()
    {
        $files = Input::file('files');
        $result = $this->controller->fireEvent('responsiv.uploader.sendFile', [$this, $files], true);
        return ['files' => $result];
    }

}
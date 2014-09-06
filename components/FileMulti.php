<?php namespace Responsiv\Uploader\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CombineAssets;

class FileMulti extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'File (Multi)',
            'description' => 'Upload multiple files'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $assets = [
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

}
<?php namespace Responsiv\Uploader\Components;

use Input;
use Cms\Classes\ComponentBase;
use System\Classes\CombineAssets;

class FileUploader extends ComponentBase
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
        $this->addJs('assets/vendor/dropzone/dropzone.js');
    }

    public function onUpload()
    {
        $files = Input::file('files');
        $result = $this->controller->fireEvent('responsiv.uploader.sendFile', [$this, $files], true);
        return ['files' => $result];
    }

}
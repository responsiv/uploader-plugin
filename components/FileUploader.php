<?php namespace Responsiv\Uploader\Components;

use Input;
use Cms\Classes\ComponentBase;
use System\Classes\CombineAssets;

class FileUploader extends ComponentBase
{

    use \Responsiv\Uploader\Traits\ComponentUtils;

    public $maxSize;
    public $placeholderText;

    /**
     * Supported file types.
     * @var array
     */
    public $fileTypes;

    /**
     * @var bool Has the model been bound.
     */
    protected $isBound = false;

    /**
     * @var bool Is the related attribute a "many" type.
     */
    public $isMulti = false;

    public function componentDetails()
    {
        return [
            'name'        => 'File Uploader',
            'description' => 'Upload a file'
        ];
    }

    public function defineProperties()
    {
        return [
            'placeholderText' => [
                'title'       => 'Placeholder text',
                'description' => 'Wording to display when no file is uploaded',
                'default'     => 'Click or drag files to upload',
                'type'        => 'string',
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
            'deferredBinding' => [
                'title'       => 'Use deferred binding',
                'description' => 'If checked the associated model must be saved for the upload to be bound.',
                'type'        => 'checkbox',
            ],
        ];
    }

    public function init()
    {
        $this->fileTypes = $this->processFileTypes();
        $this->maxSize = $this->property('maxSize');
        $this->placeholderText = $this->property('placeholderText');
    }

    public function onRun()
    {
        $this->addCss('assets/css/uploader.css');
        $this->addJs('assets/vendor/dropzone/dropzone.js');
        $this->addJs('assets/js/file-multi.js');

        if ($result = $this->checkUploadAction()) {
            return $result;
        }
    }

    public function onRender()
    {
        if (!$this->isBound)
            throw new ApplicationException('There is no model bound to the uploader!');
    }

    public function onUpload()
    {
        $files = Input::file('files');
        $result = $this->controller->fireEvent('responsiv.uploader.sendFile', [$this, $files], true);
        return ['files' => $result];
    }

    public function onUpdateFile()
    {
        $file = $this->getPopulated();

        if (($deleteId = post('id')) && post('mode') == 'delete') {
            if ($deleteFile = $file->find($deleteId)) {
                $deleteFile->delete();
            }
        }

        $this->page['file'] = $file;
    }

}
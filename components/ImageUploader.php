<?php namespace Responsiv\Uploader\Components;

use Input;
use Request;
use Validator;
use System\Models\File;
use Cms\Classes\ComponentBase;
use System\Classes\ApplicationException;
use October\Rain\Support\ValidationException;

class ImageUploader extends ComponentBase
{

    public $model;
    public $attribute;
    public $previewWidth;
    public $previewHeight;
    public $placeholderText;

    public function componentDetails()
    {
        return [
            'name'        => 'Image Uploader',
            'description' => 'Upload an image with preview'
        ];
    }

    public function defineProperties()
    {
        return [
            'placeholderText' => [
                'title'       => 'Placeholder text',
                'description' => 'Wording to display when no image is uploaded',
                'default'     => 'Click to add image',
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
            'deferredBinding' => [
                'title'       => 'Use deferred binding',
                'description' => 'If checked the associated model must be saved for the upload to be bound.',
                'type'        => 'checkbox',
            ],
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/uploader.css');
        $this->addJs('assets/js/image-single.js');
        $this->addJs('assets/vendor/dropzone/dropzone.js');

        $this->prepareVars();
        $this->checkUploadAction();
    }

    public function onRender()
    {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->previewWidth = $this->page['previewWidth'] = $this->property('previewWidth');
        $this->previewHeight = $this->page['previewHeight'] = $this->property('previewHeight');
        $this->placeholderText = $this->page['placeholderText'] = $this->property('placeholderText');
    }

    public function onUpdateImage()
    {
        $this->prepareVars();
        $this->page['image'] = $this->getPopulated();
    }

    public function bindModel($attribute, $model)
    {
        if (is_callable($model))
            $model = $model();

        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function isPopulated()
    {
        return $this->getPopulated();
    }

    public function getPopulated()
    {
        if ($sessionKey = $this->getSessionKey()) {
            return $this->model
                ->{$this->attribute}()
                ->withDeferred($sessionKey)
                ->orderBy('id', 'desc')
                ->first();
        }

        return $this->model->{$this->attribute};
    }

    protected function checkUploadAction()
    {
        $uploadedFile = Input::file('file_data');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }

        $validationRules = ['mimes:png,jpg,jpeg'];
        $validation = Validator::make(
            ['file_data' => $uploadedFile],
            ['file_data' => $validationRules]
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        if (!$uploadedFile->isValid())
            throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));

        $file = new File;
        $file->data = $uploadedFile;
        $file->is_public = true;
        $file->save();

        $this->model->{$this->attribute}()->add($file, $this->getSessionKey());
    }

    public function getSessionKey()
    {
        return !!$this->property('deferredBinding')
            ? post('_session_key')
            : null;
    }

}
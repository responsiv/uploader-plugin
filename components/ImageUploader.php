<?php namespace Responsiv\Uploader\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;

class ImageUploader extends ComponentBase
{

    use \Responsiv\Uploader\Traits\ComponentUtils;

    public $maxSize;
    public $previewWidth;
    public $previewHeight;
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
                'default'     => 'Click or drag images to upload',
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
                'default'     => '.gif,.jpg,.jpeg,.png',
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

    public function init()
    {
        $this->fileTypes = $this->processFileTypes();
        $this->maxSize = $this->property('maxSize');
        $this->previewWidth = $this->property('previewWidth');
        $this->previewHeight = $this->property('previewHeight');
        $this->placeholderText = $this->property('placeholderText');
    }

    public function onRun()
    {
        $this->addCss('assets/css/uploader.css');
        $this->addJs('assets/vendor/dropzone/dropzone.js');

        if ($this->isMulti) {
            $this->addJs('assets/js/image-multi.js');
        }
        else {
            $this->addJs('assets/js/image-single.js');
        }

        if ($result = $this->checkUploadAction()) {
            return $result;
        }
    }

    public function onRender()
    {
        if (!$this->isBound)
            throw new ApplicationException('There is no model bound to the uploader!');
    }

    public function onUpdateImage()
    {
        $image = $this->getPopulated();

        if (($deleteId = post('id')) && post('mode') == 'delete') {
            if ($deleteImage = $image->find($deleteId)) {
                $deleteImage->delete();
            }
        }

        $this->page['image'] = $image;
    }



}
<?php namespace Responsiv\Uploader\Components;

use System\Models\File;
use Cms\Classes\ComponentBase;
use ApplicationException;

class ImageUploader extends ComponentBase
{

    use \Responsiv\Uploader\Traits\ComponentUtils;

    public $maxSize;
    public $imageWidth;
    public $imageHeight;
    public $imageMode;
    public $previewFluid;
    public $placeholderText;

    /**
     * @var array Options used for generating thumbnails.
     */
    public $thumbOptions = [
        'mode'      => 'crop',
        'extension' => 'auto'
    ];

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

    /**
     * @var Collection
     */
    public $fileList;

    /**
     * @var Model
     */
    public $singleFile;

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
            'imageWidth' => [
                'title'       => 'Image preview width',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default'     => '100',
                'type'        => 'string',
            ],
            'imageHeight' => [
                'title'       => 'Image preview height',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default'     => '100',
                'type'        => 'string',
            ],
            'imageMode' => [
                'title'       => 'Image preview mode',
                'description' => 'Thumb mode for the preview, eg: exact, portrait, landscape, auto or crop',
                'default'     => 'crop',
                'type'        => 'string',
            ],
            // 'previewFluid' => [
            //     'title'       => 'Fluid preview',
            //     'description' => 'The image should expand to fit the size of its container',
            //     'default'     => 0,
            //     'type'        => 'checkbox',
            // ],
            'deferredBinding' => [
                'title'       => 'Use deferred binding',
                'description' => 'If checked the associated model must be saved for the upload to be bound.',
                'type'        => 'checkbox',
            ],
        ];
    }

    public function init()
    {
        $this->fileTypes = $this->processFileTypes(true);
        $this->maxSize = $this->property('maxSize');
        $this->imageWidth = $this->property('imageWidth');
        $this->imageHeight = $this->property('imageHeight');
        $this->imageMode = $this->property('imageMode');
        $this->previewFluid = $this->property('previewFluid');
        $this->placeholderText = $this->property('placeholderText');

        $this->thumbOptions['mode'] = $this->imageMode;
    }

    public function onRun()
    {
        $this->addCss('assets/css/uploader.css');
        $this->addJs('assets/vendor/dropzone/dropzone.js');
        $this->addJs('assets/js/uploader.js');

        if ($result = $this->checkUploadAction()) {
            return $result;
        }

        $this->fileList = $fileList = $this->getFileList();
        $this->singleFile = $fileList->first();
    }

    public function getCssBlockDimensions()
    {
        return $this->getCssDimensions('block');
    }

    /**
     * Returns the CSS dimensions for the uploaded image,
     * uses auto where no dimension is provided.
     * @param string $mode
     * @return string
     */
    public function getCssDimensions($mode = null)
    {
        if (!$this->imageWidth && !$this->imageHeight) {
            return '';
        }

        $cssDimensions = '';

        if ($mode == 'block') {
            $cssDimensions .= ($this->imageWidth)
                ? 'width: '.$this->imageWidth.'px;'
                : 'width: '.$this->imageHeight.'px;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: '.$this->imageHeight.'px;'
                : 'height: auto;';
        }
        else {
            $cssDimensions .= ($this->imageWidth)
                ? 'width: '.$this->imageWidth.'px;'
                : 'width: auto;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: '.$this->imageHeight.'px;'
                : 'height: auto;';
        }

        return $cssDimensions;
    }

    /**
     * Adds the bespoke attributes used internally by this widget.
     * - thumbUrl
     * - pathUrl
     * @return System\Models\File
     */
    protected function decorateFileAttributes($file)
    {
        $path = $thumb = $file->getPath();

        if (!empty($this->imageWidth) || !empty($this->imageHeight)) {
            $thumb = $file->getThumb($this->imageWidth, $this->imageHeight, $this->thumbOptions);
        }
        else {
            $thumb = $file->getThumb(63, 63, $this->thumbOptions);
        }

        $file->pathUrl = $path;
        $file->thumbUrl = $thumb;

        return $file;
    }

    public function onRender()
    {
        if (!$this->isBound) {
            throw new ApplicationException('There is no model bound to the uploader!');
        }

        if ($populated = $this->property('populated')) {
            $this->setPopulated($populated);
        }
    }

    public function onRemoveAttachment()
    {
        if (($file_id = post('file_id')) && ($file = File::find($file_id))) {
            $this->model->{$this->attribute}()->remove($file, $this->getSessionKey());
        }
    }

}
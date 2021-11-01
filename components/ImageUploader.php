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
            'name'        => 'responsiv.uploader::lang.component.image_uploader',
            'description' => 'responsiv.uploader::lang.component.image_uploader_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'placeholderText' => [
                'title'       => 'responsiv.uploader::lang.prop.placeholder',
                'description' => 'responsiv.uploader::lang.prop.placeholder_img_desc',
                'default'     => 'Click or drag images to upload',
                'type'        => 'string',
            ],
            'maxSize' => [
                'title'       => 'responsiv.uploader::lang.prop.maxSize',
                'description' => 'responsiv.uploader::lang.prop.maxSize_desc',
                'default'     => '5',
                'type'        => 'string',
            ],
            'fileTypes' => [
                'title'       => 'responsiv.uploader::lang.prop.fileTypes',
                'description' => 'responsiv.uploader::lang.prop.fileTypes_desc',
                'default'     => '.gif,.jpg,.jpeg,.png',
                'type'        => 'string',
            ],
            'imageWidth' => [
                'title'       => 'responsiv.uploader::lang.prop.imageWidth',
                'description' => 'responsiv.uploader::lang.prop.imageWidth_desc',
                'default'     => '100',
                'type'        => 'string',
            ],
            'imageHeight' => [
                'title'       => 'responsiv.uploader::lang.prop.imageHeight',
                'description' => 'responsiv.uploader::lang.prop.imageHeight_desc',
                'default'     => '100',
                'type'        => 'string',
            ],
            'imageMode' => [
                'title'       => 'responsiv.uploader::lang.prop.imageMode',
                'description' => 'responsiv.uploader::lang.prop.imageMode_desc',
                'default'     => 'crop',
                'type'        => 'string',
            ],
            // 'previewFluid' => [
            //     'title'       => 'responsiv.uploader::lang.prop.previewFluid',
            //     'description' => 'responsiv.uploader::lang.prop.previewFluid_desc',
            //     'default'     => 0,
            //     'type'        => 'checkbox',
            // ],
            'deferredBinding' => [
                'title'       => 'responsiv.uploader::lang.prop.deferredBinding',
                'description' => 'responsiv.uploader::lang.prop.deferredBinding_desc',
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
        $this->addCss(['assets/css/uploader.css']);
        $this->addJs([
            'assets/vendor/dropzone/dropzone.js',
            'assets/js/uploader.js',
        ]);

        $this->autoPopulate();
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
                ? 'width: ' . $this->imageWidth . 'px;'
                : 'width: ' . $this->imageHeight . 'px;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: ' . $this->imageHeight . 'px;'
                : 'height: auto;';
        }
        else {
            $cssDimensions .= ($this->imageWidth)
                ? 'width: ' . $this->imageWidth . 'px;'
                : 'width: auto;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: ' . $this->imageHeight . 'px;'
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
        else {
            $this->autoPopulate();
        }
    }
}

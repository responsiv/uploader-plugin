<?php namespace Responsiv\Uploader\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;

class FileUploader extends ComponentBase
{
    use \Responsiv\Uploader\Traits\ComponentUtils;

    /**
     * @var array fileTypes supported
     */
    public $fileTypes;

    /**
     * @var bool isBound determines if the model been bound.
     */
    protected $isBound = false;

    /**
     * @var bool isMulti true if the related attribute a "many" type.
     */
    public $isMulti = false;

    /**
     * @var Collection fileList
     */
    public $fileList;

    /**
     * @var Model singleFile
     */
    public $singleFile;

    /**
     * @var string|int maxSize
     */
    public $maxSize;

    /**
     * @var string placeholderText
     */
    public $placeholderText;

    public function componentDetails()
    {
        return [
            'name' => 'responsiv.uploader::lang.component.file_uploader',
            'description' => 'responsiv.uploader::lang.component.file_uploader_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'placeholderText' => [
                'title' => 'responsiv.uploader::lang.prop.placeholder',
                'description' => 'responsiv.uploader::lang.prop.placeholder_file_desc',
                'default' => 'Click or drag files to upload',
                'type' => 'string',
            ],
            'maxSize' => [
                'title' => 'responsiv.uploader::lang.prop.maxSize',
                'description' => 'responsiv.uploader::lang.prop.maxSize_desc',
                'default' => '5',
                'type' => 'string',
            ],
            'fileTypes' => [
                'title' => 'responsiv.uploader::lang.prop.fileTypes',
                'description' => 'responsiv.uploader::lang.prop.fileTypes_desc',
                'default' => '*',
                'type' => 'string',
            ],
            'deferredBinding' => [
                'title' => 'responsiv.uploader::lang.prop.deferredBinding',
                'description' => 'responsiv.uploader::lang.prop.deferredBinding_desc',
                'type' => 'checkbox',
            ],
        ];
    }

    /**
     * init
     */
    public function init()
    {
        $this->fileTypes = $this->processFileTypes(true);
        $this->maxSize = $this->property('maxSize');
        $this->placeholderText = $this->property('placeholderText');
    }

    /**
     * onRun
     */
    public function onRun()
    {
        $this->addCss(['assets/css/uploader.css']);
        $this->addJs([
            'assets/vendor/dropzone/dropzone.js',
            'assets/js/uploader.js',
        ]);

        $this->autoPopulate();
    }

    /**
     * onRender
     */
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

    /**
     * decorateFileAttributes adds the bespoke attributes used internally by this widget.
     * - thumbUrl
     * - pathUrl
     * @return System\Models\File
     */
    protected function decorateFileAttributes($file)
    {
        $file->pathUrl = $file->thumbUrl = $file->getPath();

        return $file;
    }
}

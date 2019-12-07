<?php namespace Responsiv\Uploader\Traits;

use Input;
use Request;
use Response;
use Validator;
use System\Models\File;
use October\Rain\Support\Collection;
use October\Rain\Filesystem\Definitions;
use ApplicationException;
use ValidationException;
use Exception;

trait ComponentUtils
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $sessionKey;

    public function bindModel($attribute, $model)
    {
        if (is_callable($model)) {
            $model = $model();
        }

        $this->model = $model;
        $this->attribute = $attribute;

        if ($this->model) {
            $relationType = $this->model->getRelationType($attribute);
            $this->isMulti = ($relationType == 'attachMany' || $relationType == 'morphMany');
            $this->isBound = true;
        }
    }

    public function autoPopulate()
    {
        if (!$this->isPopulated()) {
            $this->fileList = $fileList = $this->getFileList();
            $this->singleFile = $fileList->first();
        }
    }

    public function setPopulated($model)
    {
        $list = $this->isMulti ? $model : new Collection([$model]);

        $list->each(function($file) {
            $this->decorateFileAttributes($file);
        });

        $this->fileList = $list;
        $this->singleFile = $list->first();
    }

    public function isPopulated()
    {
        if (!$this->fileList) {
            return false;
        }

        return $this->fileList->count() > 0;
    }

    public function getFileList()
    {
        if (!is_string($this->attribute)) {
            throw new ApplicationException(sprintf('Attribute name must be a string, %s was passed.', gettype($this->attribute)));
        }

        /*
         * Use deferred bindings
         */
        if ($sessionKey = $this->getSessionKey()) {
            $list = $deferredQuery = $this->model
                ->{$this->attribute}()
                ->withDeferred($sessionKey)
                ->orderBy('id', 'desc')
                ->get();
        }
        else {
            $list = $this->model
                ->{$this->attribute}()
                ->orderBy('id', 'desc')
                ->get();
        }

        if (!$list) {
            $list = new Collection;
        }

        /*
         * Decorate each file with thumb
         */
        $list->each(function($file) {
            $this->decorateFileAttributes($file);
        });

        return $list;
    }

    public function onUpload()
    {
        try {
            if (!Input::hasFile('file_data')) {
                throw new ApplicationException('File missing from request');
            }

            $uploadedFile = Input::file('file_data');


            $validationRules = ['max:'.$this->getMaxFileSize()];
            if ($fileTypes = $this->processFileTypes()) {
                $validationRules[] = 'extensions:'.$fileTypes;
            }

            $validation = Validator::make(
                ['file_data' => $uploadedFile],
                ['file_data' => $validationRules]
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            if (!$uploadedFile->isValid()) {
                throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));
            }

            $relationDefinition = $this->model->getRelationDefinition($this->attribute);
            $fileModel = $relationDefinition[0];
            $isPublic = array_get($relationDefinition, 'public', true);

            $file = new $fileModel;
            $file->data = $uploadedFile;
            $file->is_public = $isPublic;
            $file->save();

            $this->model->{$this->attribute}()->add($file, $this->getSessionKey());

            $file = $this->decorateFileAttributes($file);

            $result = [
                'id' => $file->id,
                'thumb' => $file->thumbUrl,
                'path' => $file->pathUrl
            ];

            return Response::json($result, 200);

        }
        catch (Exception $ex) {
            return Response::json($ex->getMessage(), 400);
        }
    }

    public function getSessionKey()
    {
        return !!$this->property('deferredBinding')
            ? post('_session_key', $this->sessionKey)
            : null;
    }

    /**
     * Returns the specified accepted file types, or the default
     * based on the mode. Image mode will return:
     * - jpg,jpeg,bmp,png,gif,svg
     * @return string
     */
    protected function processFileTypes($includeDot = false)
    {
        $types = $this->property('fileTypes', '*');

        if (!$types || $types == '*') {
            $types = implode(',', Definitions::get('defaultExtensions'));
        }

        if (!is_array($types)) {
            $types = explode(',', $types);
        }

        $types = array_map(function($value) use ($includeDot) {
            $value = trim($value);

            if (substr($value, 0, 1) == '.') {
                $value = substr($value, 1);
            }

            if ($includeDot) {
                $value = '.'.$value;
            }

            return $value;
        }, $types);

        return implode(',', $types);
    }

    /**
     * Get the max File Size
     * @return int
     */
    protected function getMaxFileSize()
    {
        if ($maxSize = $this->property('maxSize')) {
            return round($maxSize * 1024);
        }
        else {
            return File::getMaxFilesize();
        }
    }
}

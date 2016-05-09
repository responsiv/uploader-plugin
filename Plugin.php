<?php namespace Responsiv\Uploader;

use System\Classes\PluginBase;

/**
 * Uploader Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Uploader',
            'description' => 'Tools for uploading files and photos',
            'author'      => 'Responsiv Internet',
            'icon'        => 'icon-leaf',
            'homepage'    => 'https://github.com/responsiv/uploader-plugin'
        ];
    }

    public function registerComponents()
    {
        return [
           'Responsiv\Uploader\Components\FileUploader'  => 'fileUploader',
           'Responsiv\Uploader\Components\ImageUploader' => 'imageUploader',
        ];
    }
}

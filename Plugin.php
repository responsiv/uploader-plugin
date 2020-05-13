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
            'name'        => 'responsiv.uploader::lang.plugin.name',
            'description' => 'responsiv.uploader::lang.plugin.desc',
            'author'      => 'Responsiv Internet',
            'icon'        => 'icon-download',
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

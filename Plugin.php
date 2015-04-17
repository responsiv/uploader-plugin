<?php namespace Responsiv\Uploader;

use System\Classes\PluginBase;
use System\Classes\CombineAssets;

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
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('$/responsiv/uploader/assets/less/uploader.less');
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     * @return array
     */
    public function registerComponents()
    {
        return [
           'Responsiv\Uploader\Components\FileUploader'  => 'fileUploader',
           'Responsiv\Uploader\Components\ImageUploader' => 'imageUploader',
        ];
    }

}

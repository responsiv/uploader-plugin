<?php

return [
    'plugin' => [
        'name' => 'Uploader',
        'desc' => 'Tools for uploading files and photos'
    ],
    'component' => [
        'file_uploader' => 'File Uploader',
        'file_uploader_desc' => 'Upload a file',
        'image_uploader' => 'Image Uploader',
        'image_uploader_desc' => 'Upload an image with preview'
    ],
    'prop' => [
        'placeholder' => 'Placeholder text',
        'placeholder_file_desc' => 'Wording to display when no file is uploaded',
        'placeholder_img_desc' => 'Wording to display when no image is uploaded',
        'maxSize' => 'Max file size (MB)',
        'maxSize_desc' => 'The maximum file size that can be uploaded in megabytes.',
        'fileTypes' => 'Supported file types',
        'fileTypes_desc' => 'File extensions separated by commas (,) or star (*) to allow all types.',
        'imageWidth' => 'Image preview width',
        'imageWidth_desc' => 'Enter an amount in pixels, eg: 100',
        'imageHeight' => 'Image preview height',
        'imageHeight_desc' => 'Enter an amount in pixels, eg: 100',
        'imageMode' => 'Image preview mode',
        'imageMode_desc' => 'Thumb mode for the preview, eg: exact, portrait, landscape, auto or crop',
        // 'previewFluid' => 'Fluid preview',
        // 'previewFluid_desc' => 'The image should expand to fit the size of its container',
        'deferredBinding' => 'Use deferred binding',
        'deferredBinding_desc' => 'If checked the associated model must be saved for the upload to be bound.',
    ],
    'are_you_sure' => 'Are you sure?'
];

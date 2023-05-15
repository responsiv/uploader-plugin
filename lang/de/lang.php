<?php

return [
    'plugin' => [
        'name' => 'Uploader',
        'desc' => 'Werkzeuge um Dateien und Bilder hochzuladen'
    ],
    'component' => [
        'file_uploader' => 'Dateiupload',
        'file_uploader_desc' => 'Zeigt einen Dateiupload an',
        'image_uploader' => 'Bildupload',
        'image_uploader_desc' => 'Zeigt einen Bildupload mit Vorschau an'
    ],
    'prop' => [
        'placeholder' => 'Platzhalter Text',
        'placeholder_file_desc' => 'Angezeigter Text wenn keine noch keine Dateien hochgeladen wurden',
        'placeholder_img_desc' => 'Angezeigter Text wenn keine noch keine Bilder hochgeladen wurden',
        'maxSize' => 'Max. Dateigrösse (MB)',
        'maxSize_desc' => 'The maximale Dateigrösse in Megabytes.',
        'fileTypes' => 'Erlaube Dateitypen',
        'fileTypes_desc' => 'Komma-getrennte Dateiendungen oder Stern (*) um alles zu erlauben.',
        'imageWidth' => 'Breite der Bildvorschau',
        'imageWidth_desc' => 'Breite in Pixeln eingeben: z. B. 100',
        'imageHeight' => 'Höhe der Bildvorschau',
        'imageHeight_desc' => 'Höhe in Pixeln eingeben: z. B. 100',
        'imageMode' => 'Modus für Bildvorschau',
        'imageMode_desc' => 'Darstellung der Bildvorschau: exact, portrait, landscape, auto oder crop',
        'deferredBinding' => 'Deferred Binding verwenden',
        'deferredBinding_desc' => 'Wenn aktiviert, muss das verwandte Model gespeichert werden, um die hochgeladene Datei definitiv zu verknüpfen.',
    ],
    'are_you_sure' => 'Sind sie sicher?'
];

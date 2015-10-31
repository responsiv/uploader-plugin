# Uploader Control

Collection of File Upload components for October. This is a port of the back-end `fileupload` form widget. There are two primary components provided by this plugin: image uploader and file uploader. The image upload is suitable for uploading photos with thumbnails, whereas the file uploader is suitable for any type of file.

Each component will detect if the relationship is a multiple ("many") or singular ("one") type and render a different control accordingly.

#### Attaching the uploader (from page)

To attach the uploader directly to the page as a component, you simply initialize the component either on the page or layout:

    [fileUploader]
    maxSize = "2"
    fileTypes = "*"

The next step requires a model object and attribute name, take this model for example:

    class Project extends Model
    {
        public $attachMany = [
            'files' => 'System\Models\File',
        ];
    }

This model has a *many* attachment relation called **files**. We must tell the uploader that this is the relation we want to use for saving the uploads. This should be done in the `onInit` funtion of the page or layout.

    function onInit()
    {
        $this->fileUploader->bindModel('files', Project::find(1));
    }

This example shows that the uploaded files will be attached to the **Project** model with the record ID of **1** using the relation called **files**.

Finally we need to render the uploader component on the page using the `{% component %}` tag. The component should be wrapped in a `<form>` element but this is optional. Here is a complete example of how the page might look:

    title = "Project"
    url = "/project"
    layout = "default"

    [fileUploader]
    maxSize = "2"
    fileTypes = "*"
    ==
    <?php
    function onInit()
    {
        $this->fileUploader->bindModel('files', Project::find(1));
    }
    ?>
    ==

    <form>
        <!-- File uploader -->
        {% component 'fileUploader' %}
    </form>

#### Attaching the uploader (from component)

The most effective way to use this plugin is to attach the uploader from another component. Override the `init` method to initialize the uploader inside your component class with the `addComponent` method.

    class MyComponent extends ComponentBase
    {
        public function init()
        {
            $component = $this->addComponent(
                'Responsiv\Uploader\Components\FileUploader',
                'fileUploader',
                ['deferredBinding' => false]
            );

            $component->bindModel('files', Project::find(1));
        }
    }

The component can then be rendered on the page as normal:

    <form>
        <!-- File uploader -->
        {% component 'fileUploader' %}
    </form>

As a side note, if you wish to refresh the component via an AJAX handler, ensure that you call the `pageCycle` method to initialize the page components.

    public function onRefreshFiles()
    {
        $this->pageCycle();
    }

#### Uploader with deferred binding

You may wish to upload a file and attach it to a model that doesn't exist yet. This is possible using deferred bindings, a feature built-in to October.

The first step is to tell the uploader that we are using deferred bindings by setting the component property `deferredBinding` to 1.

    [fileUploader]
    deferredBinding = "1"

The `onInit` function will still require a instance of the Model object to locate the relationship details. Since the model doesn't exist, we pass an unfilled/prepared model object:

    function onInit()
    {
        $this->fileUploader->bindModel('files', new Project);
    }

When rendering the uploader on the page, it is important to use the `{{ form_open() }}` twig helper, this will ensure a session key is generated and passed along with the form. The server will receive this session key as `_session_key`.

    {{ form_open() }}
        <!-- File uploader -->
        {% component 'fileUploader' %}
    {{ form_close() }}

Now when a file is uploaded, the relationship binding will be deferred until the model is saved using the supplied session key. Here is an example of the saving process for the Project model:

    public function onSave()
    {
        $project = new Project;
        $project->title = 'Hello';
        $project->save(null, post('_session_key'));
    }

Notice the second argument on the `save()` method called above, this will contain the same session key used by the uploader and will add or remove any uploaded files.

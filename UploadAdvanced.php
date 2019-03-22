<?php
namespace kak\storage;

use yii\base\Widget;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;


/*
 <input name="files" id="files" type="file" data-role="upload" webkitdirectory="true" directory="true" multiple="multiple" autocomplete="off">
 */

/**
 *
 *
 *
 * Class Upload Widget
 * @usage
 * ```php
    <?= \kak\storage\Upload::widget([
        'model' => $upload_form,
        'label_btn' => 'Select File',
        'auto_upload' => true,
        'multiple' => true,
        'url' => '/upload'
    ]); ?>
 * ```
 */
class UploadAdvanced extends Widget
{
	/**
	 * @var Model the data model that this widget is associated with.
	 */
	public $model;
    /***
     * Config JQuery Upload File
     */
    public $url = '/upload';

    public $autoUpload = true;
	public $multiple = true;
    public $progressbarAll = false;
    public $crop = true;
    public $singleUpload = false;

    public $options = [];
    public $view = 'advanced';


    public $labelBtn               = 'Add files...';
    public $labelSuccess           = 'uploaded success';
    public $labelProcessingUpload  = 'Processing upload...';
    public $labelUploadError       = 'uploading error...';
    public $labelCrop              = 'Crop';
    public $labelStart             = 'Start';
    public $labelCancel            = 'Cancel';
    public $labelDelete            = 'Delete';

    /**
     * Init widget
     */
	public function init()
	{
		parent::init();
        $this->registerAssets();
        $this->url = Url::to($this->url);
        $this->options['multiple']  = ($this->multiple == true);

        if(!$this->id ) {
            $class = StringHelper::basename(get_class($this->model));
            $this->id = array_pop($class). '-form';
        }
        if(!isset($this->options['id'])) {
            $this->options['id'] = $this->id . '-upload-btn';
        }
	}

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        bundles\StorageAsset::register($view);
    }
    /**
     * Run widget
     * @return string
     */
	public function run()
	{
        $id   = $this->id;
        $view = $this->getView();

        $pluginOptions = [
            'url' => $this->url
        ];

        $this->prepatePluginOptionsWithAuth($pluginOptions);

        $pluginOptionsString = Json::htmlEncode($pluginOptions);
        $js = "jQuery('#{$id}').kakStorageAdvancedUpload({$pluginOptionsString})";
        $view->registerJs($js,$view::POS_READY, $id . ':kak-storage-advanced-upload ');

		return $this->render($this->view ,[
			'model'       => $this->model,
            'options'     => $this->options
		]);
	}

    /**
     * @return yii\authclient\Collection|null
     */
	private function getAuthClientCollection()
    {
        $authClientCollection = \Yii::$app->get('authClientCollection', false);
        return $authClientCollection;
    }

    /**
     * @param array $options
     */
	private function prepatePluginOptionsWithAuth(array &$options): void
    {
        $authClientCollection = $this->getAuthClientCollection();
        if(!$authClientCollection) {
            return;
        }

        /*
         /client_id=CLIENT-ID&redirect_uri=REDIRECT-URI&response_type=code
         */



        if($authClientCollection->hasClient('instagram')){
            /** @var $client \kak\authclient\Instagram  */
            $client = $authClientCollection->getClient('instagram');
            $options['instagram'] = [
                'authUrl' => $client->buildAuthUrl([
                    'redirect_uri' => Url::to('/storage/auth/token?id=instgram', true)
                ])
            ];
        }

        if($authClientCollection->hasClient('dropbox')){

        }

        if($authClientCollection->hasClient('facebook')){

        }

        if($authClientCollection->hasClient('vkontakte')){

        }
    }


	/**
	 * @return boolean whether this widget is associated with a data model.
	 */
	protected function hasModel()
	{
		return $this->model instanceof Model;
	}

}
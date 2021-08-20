<?php
namespace d4yii2\yii2\wiki\models;

use eaBlankonThema\components\FlashHelper;
use Yii;
use d4yii2\yii2\wiki\Module;

/**
 * This is the model class for table "wiki".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 *
 * @property bool $isOrphan
 * @property string $contentProcessed
 *
 * @license MIT
 */
class Wiki extends \yii\db\ActiveRecord
{

    private static $dir;
    public $img;

    function __construct($config = [])
    {
        self::$dir = Yii::getAlias('@runtime') . '/wiki/';

        if(!file_exists(self::$dir)) {
            mkdir(self::$dir);
        }
        parent::__construct($config);
    }

    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'wiki';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['content'], 'default'],

			[['id', 'title'], 'required'],

			[['id'], 'match', 'pattern'=>Module::getInstance()->articleIdRegex, 'message'=>Module::getInstance()->invalidArticleIdMessage],
			[['content'], 'string'],
			[['id', 'title'], 'string', 'max'=>255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'Identifier'),
			'title' => Yii::t('app', 'Title'),
			'content' => Yii::t('app', 'Content'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function find()
	{
		$queryClass = Module::getInstance()->queryClass;
		return new $queryClass(get_called_class());
	}

	public static function findOne($condition)
    {
        if(!isset($condition['id'])) {
            return false;
        }
        $model = null;
        $fileName = Yii::getAlias('@runtime') . '/wiki/' . $condition['id'] . '.txt';
        try {
            if(file_exists($fileName)) {
                $model = new self();
                $model->id = $condition['id'];
                $model->content = file_get_contents($fileName);
            } else {
                $create = fopen($fileName, 'w');
                fclose($create);
                $model = new self();
                $model->id = $condition['id'];
            }

        } catch (\Exception $e) {
            Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return $model;
    }

    public function load($data, $formName = null, $img = null)
    {
        if(!empty($img)) {
            $allowed = array('jpeg', 'png', 'jpg');
            $filename = $img['name']['img'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowed)) {
                FlashHelper::addDanger('Unsupported file type !');
                return false;
            }
            if($img['size']['img'] > 2 * 1024 * 1024) { // 2MB
                FlashHelper::addDanger('Image is too big !');
                return false;
            }
        }
        return parent::load($data, $formName);
    }

    public function save($runValidation = true, $attributeNames = null, $img = null)
    {
        if(self::findOne(['id' => $this->id])) {
            try {
                if(!empty($img)) {
                    $imgContent = file_get_contents($img['tmp_name']['img']);
                    file_put_contents(self::$dir.$img['name']['img'],$imgContent);
                }
                file_put_contents(self::$dir . $this->id . '.txt', $this->content);
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }
    }

    public function getIsOrphan()
	{
		return !static::find()->withLinkToArticle($this->id)->exists();
	}

	public function getContentProcessed()
	{
		return Module::getInstance()->processContent($this->content);
	}

	public function getAttachments()
    {
        $attachments = glob(self::$dir . "/*.{jpg,png,jpeg,JPG,PNG,JPEG}", GLOB_BRACE);
        $result = [];
        foreach ($attachments as $key => $path) {
            $result[$key]['path'] = $path;
            $result[$key]['name'] = basename($path);
        }
        return $result;
    }

}

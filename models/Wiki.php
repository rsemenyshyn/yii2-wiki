<?php
namespace asinfotrack\yii2\wiki\models;

use Yii;
use asinfotrack\yii2\wiki\Module;

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
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class Wiki extends \yii\db\ActiveRecord
{

    private static $dir;

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

    public function save($runValidation = true, $attributeNames = null)
    {
        if(self::findOne(['id' => $this->id])) {
            try {
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

}

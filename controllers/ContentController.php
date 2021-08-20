<?php
namespace d4yii2\yii2\wiki\controllers;

use d4yii2\yii2\wiki\models\Wiki;
use eaBlankonThema\components\FlashHelper;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use d4yii2\yii2\wiki\Module;


/**
 * WikiController implements the CRUD actions for Wiki model.
 *
 */
class ContentController extends Controller
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{

        if($rolesCanEdit = Module::getInstance()->rolesCanEdit){
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'delete',
                                'admin',
                                'create',
                                'update',
                                'show-image',
                                'delete-image'
                            ],
                            'roles' => $rolesCanEdit,
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'index',
                                'view'
                            ],
                            'roles' => Module::getInstance()->rolesCanView,
                        ],
                    ],
                ],
            ];

        }

        return [
            'verbs'=>[
                'class'=>VerbFilter::className(),
                'actions'=>[
                    'delete'=>['post'],
                ],
            ],
        ];

	}

    public function beforeAction($action)
    {
        if(Module::getInstance()->layout) {
            $this->layout = Module::getInstance()->layout;
        }
        return parent::beforeAction($action);
    }

	/**
	 * Admin action to manage wiki pages
	 *
	 * @return string
	 */
	public function actionAdmin()
	{
		$searchModelClassName = Module::getInstance()->searchModelClass;
		$searchModel = new $searchModelClassName();
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render(Module::getInstance()->viewMap['admin'], [
			'dataProvider'=>$dataProvider,
			'searchModel'=>$searchModel,
		]);
	}

	/**
	 * Redirection to the index wiki article
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$articleId = Module::getInstance()->indexArticleId;
		return $this->redirect(['view', 'id'=>$articleId]);
	}

	/**
	 * Displays a single Wiki model.
	 *
	 * @param string $id the id of the article to show
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);
		if ($model === null) {
			return $this->redirect(['create', 'id'=>$id]);
		}
		$model->content = nl2br($model->content);
		return $this->render(Module::getInstance()->viewMap['view'], [
			'model'=>$model,
		]);
	}

	/**
	 * Creates a new Wiki model. If creation is successful, the browser will be
	 * redirected to the 'view' page of the created article.
	 *
	 * @param string $id the id of the article to create
	 * @return mixed
	 */
	public function actionCreate($id)
	{
		//redirect to valid id if the current one is not invalid
		if (!Module::getInstance()->isValidArticleId($id)) {
			$validId = Module::getInstance()->createArticleId($id);
			return $this->redirect(['create', 'id'=>$validId]);
		}

		//get the model class name
		/** @var Wiki $modelClassName */
        $modelClassName = Module::getInstance()->modelClass;

		//if the id already exists, go to update action
		$model = $modelClassName::findOne($id);
		if ($model !== null) {
			return $this->redirect(['update', 'id'=>$model->id]);
		}

		//otherwise create it
		$model = new $modelClassName();
		$loaded = $model->load(Yii::$app->request->post());
		if (!$loaded) $model->id = strtolower($id);

		if ($loaded && $model->save()) {
			return $this->redirect(['view', 'id'=>$model->id]);
		}

        return $this->render(Module::getInstance()->viewMap['create'], [
            'model'=>$model,
        ]);

    }

	/**
	 * Updates an existing Wiki model. If the update is successful, the browser
	 * will be redirected to the 'view' page of the updated article
	 *
	 * @param string $id the id of the article to update
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model === null) return $this->redirect(['create', 'id'=>$id]);

        $img = isset($_FILES['Wiki']) && !empty($_FILES['Wiki']['name']['img']) ? $_FILES['Wiki'] : '';

		if ($model->load(Yii::$app->request->post(),null ,$img) && $model->save(true, null, $img)) {
			return $this->redirect(['view', 'id'=>$model->id]);
		}

        return $this->render('update', [
            'model'=>$model,
            'attachments' => $model->getAttachments()
        ]);

	}

	/**
	 * Deletes an existing Wiki model. If the deletion is successful, the browser
	 * will be redirected to the 'index' page.
	 *
	 * @param string $id id of the article to be deleted
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Wiki model based on its primary key value. If the model is not found,
	 * a 404 HTTP exception will be thrown.
	 *
	 * @param string $id id of the article to find
	 * @return \d4yii2\yii2\wiki\models\Wiki the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		$modelClassName = Module::getInstance()->modelClass;

		if (($model = $modelClassName::findOne(['id' => $id])) !== null) {
			return $model;
		}
		return null;

	}

	public function actionShowImage(string $img)
    {
        $response = \Yii::$app->response;
        $response->format = yii\web\Response::FORMAT_RAW;
        $response->headers->add('content-type', 'image/jpg');
        $img_data = file_get_contents(Yii::getAlias('@runtime') . '/wiki/'.$img);
        $response->data = $img_data;
        return $response;
    }

    public function actionDeleteImage(string $img, string $id)
    {
        try {
            unlink(Yii::getAlias('@runtime') . '/wiki/'.$img);
        } catch (\Exception $e) {
            FlashHelper::addDanger($e->getMessage());
        }
        return $this->redirect(['update', 'id' => $id]);
    }
}

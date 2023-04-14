<?php

namespace backend\controllers;

use common\models\Season;
use common\models\Episode;
use common\models\User;
use common\models\SeasonSearch;
use common\models\EpisodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\helpers\Json;
use yii\filters\AccessControl;

/**
 * SeasonController implements the CRUD actions for Season model.
 */
class SeasonController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
				'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'update', 'delete', 'create'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
				
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Season models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SeasonSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Season model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $currentAction="VIEW", $episodeId=0)
    {
        $searchModel = new EpisodeSearch();
        $searchModel->season = $id;
        $dataProvider = $searchModel->search($this->request->queryParams);
         
        if($currentAction=="UPDATE") 
            $modelEpisode = Episode::findOne($episodeId);
        elseif($currentAction=="DELETE"){
            $modelEpisode = Episode::findOne($episodeId);
            $modelEpisode->delete();
            $modelEpisode = new Episode();
        }else{
            $modelEpisode = new Episode();
            $modelEpisode->season = $id;
        }

        if ($modelEpisode->load(Yii::$app->request->post())){
            $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
            $modelEpisode->association = $modelCurrentUser->association;
            $modelEpisode->meeting_date = $_POST["Episode"]["meeting_date"];
            $modelEpisode->save();
            $currentAction = "VIEW";

            //print_r($modelEpisode->getErrors());

        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentAction' => $currentAction,
            'modelEpisode' => $modelEpisode,
        ]);
    }

    /**
     * Creates a new Season model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Season();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $model->association = $modelCurrentUser->association;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $model->generateEpisodes();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Season model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Season model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Season model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Season the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Season::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

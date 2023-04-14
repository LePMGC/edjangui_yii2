<?php

namespace backend\controllers;

use common\models\DjanguiContribution;
use common\models\DjanguiContributionSearch;
use common\models\Djangui;
use common\models\Season;
use common\models\Episode;
use common\models\DjanguiMember;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;

/**
 * DjanguiContributionController implements the CRUD actions for DjanguiContribution model.
 */
class DjanguiContributionController extends Controller
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
                            'actions' => ['index', 'view', 'update', 'delete', 'create', 'get-expected-djangui-contribution-amount'],
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
     * Lists all DjanguiContribution models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DjanguiContributionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DjanguiContribution model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DjanguiContribution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DjanguiContribution();
        $modelEpisode = Episode::find()->where("start_date <= '".date('Y-m-d')."' and '".date('Y-m-d')."' <= end_date")->one();
        $model->episode = is_null($modelEpisode) ? 0 : $modelEpisode->id;
        $model->djangui = Djangui::getFirstCreatedDjanguiId();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
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
     * Updates an existing DjanguiContribution model.
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
     * Deletes an existing DjanguiContribution model.
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
     * Finds the DjanguiContribution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DjanguiContribution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DjanguiContribution::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
    * Djangui contribution amount a memeber has to contribute
    */

    public function actionGetExpectedDjanguiContributionAmount($userId) {
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $currentSeason = Season::findOne(Season::getCurrentSeasonId());
        $djanguiModels = Djangui::find()->where(['association' => $modelCurrentUser->association])->all();
        $expected_djangui_contribution_amounts = "";
        foreach ($djanguiModels as $djanguiModel) {
            $expected_djangui_contribution_amounts = $expected_djangui_contribution_amounts."djangui_contribution_".$djanguiModel->id."=".($djanguiModel->amount * DjanguiMember::getMemberNumberOfNames($userId, $djanguiModel->id))."#"; 
        }

        echo Json::encode([
            'expected_djangui_contribution_amounts' => $expected_djangui_contribution_amounts,
        ]);  
    }
}

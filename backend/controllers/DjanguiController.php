<?php

namespace backend\controllers;

use common\models\Djangui;
use common\models\DjanguiMember;
use common\models\DjanguiMemberSearch;
use common\models\Season;
use common\models\DjanguiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use Yii;
use yii\filters\AccessControl;

/**
 * DjanguiController implements the CRUD actions for Djangui model.
 */
class DjanguiController extends Controller
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
                            'actions' => ['index', 'view', 'update', 'delete', 'create', 'members'],
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
     * Lists all Djangui models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DjanguiSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Djangui model.
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
     * Creates a new Djangui model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Djangui();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $model->association = $modelCurrentUser->association;
        $model->penalty_account = 0;

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
     * Updates an existing Djangui model.
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
     * Deletes an existing Djangui model.
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
     * Finds the Djangui model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Djangui the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Djangui::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


     /**
     * Updates members of an existing Djangui model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMembers($seasonId=0, $djanguiId=0, $currentAction='NEW', $djanguiMemberId=0){

        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        
        //get last season model
        if($seasonId === 0) $seasonId = Season::getCurrentSeasonId();

        //get last djangui model
        if($djanguiId === 0) $djanguiId = Djangui::getLastCreatedDjanguiId();

        $modelSeason = Season::findOne($seasonId);
        $modelDjangui = Djangui::findOne($djanguiId);

        
        if(strcmp($currentAction, 'NEW')==0){
            $modelDjanguiMember = new DjanguiMember();
            $modelDjanguiMember->season = $seasonId;
            $modelDjanguiMember->djangui = $djanguiId;
            $modelDjanguiMember->association = $modelCurrentUser->association;

        }elseif(strcmp($currentAction, 'UPDATE')==0){
            $modelDjanguiMember = DjanguiMember::findOne($djanguiMemberId);
            //$currentAction = 'NEW';
        }elseif(strcmp($currentAction, 'DELETE')==0){
            $modelDjanguiMember = DjanguiMember::findOne($djanguiMemberId);
            $modelDjanguiMember->delete();
           	$this->redirect(['members', 
            	'seasonId' => $seasonId,
            	'djanguiId' => $djanguiId,
            	'currentAction' => 'NEW',
            ]);
        }

        if ($modelDjanguiMember->load(Yii::$app->request->post())){
            $djanguiId = $modelDjanguiMember->djangui;
            if(isset($_POST['save']))$modelDjanguiMember->save();
                $modelDjanguiMember = new DjanguiMember();
            $this->redirect(['members', 
            	'seasonId' => $seasonId,
            	'djanguiId' => $djanguiId,
            	'currentAction' => 'NEW',
            ]);
        }

        $djanguiMemberSearchModel = new DjanguiMemberSearch();
        $djanguiMemberSearchModel->season = $seasonId;
        $djanguiMemberSearchModel->djangui = $djanguiId;
        $membersDataProvider = $djanguiMemberSearchModel->search([]);

        return $this->render('members', [
            'modelSeason' => $modelSeason,
            'modelDjangui' => $modelDjangui,
            'membersDataProvider' => $membersDataProvider,
            'modelDjanguiMember' => $modelDjanguiMember,
            'currentAction' => $currentAction
        ]);
    }
}

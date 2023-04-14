<?php

namespace backend\controllers;

use common\models\LoanOption;
use common\models\User;
use common\models\LoanOptionSearch;
use common\models\LoanOptionTermSearch;
use common\models\LoanOptionTerm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\helpers\Json;
use yii\filters\AccessControl;

/**
 * LoanOptionController implements the CRUD actions for LoanOption model.
 */
class LoanOptionController extends Controller
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
     * Lists all LoanOption models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LoanOptionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LoanOption model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $currentAction="VIEW", $loanOptionTermId=0)
    {
        $searchModel = new LoanOptionTermSearch();
        $searchModel->loan_option = $id;
        $dataProvider = $searchModel->search($this->request->queryParams);
         
        if($currentAction=="UPDATE") 
            $modelLoanOptionTerm = LoanOptionTerm::findOne($loanOptionTermId);
        elseif($currentAction=="DELETE"){
            $modelLoanOptionTerm = LoanOptionTerm::findOne($loanOptionTermId);
            $modelLoanOptionTerm->delete();
            $modelLoanOptionTerm = new LoanOptionTerm();
        }else{
            $modelLoanOptionTerm = new LoanOptionTerm();
            $modelLoanOptionTerm->loan_option = $id;
        }

        if ($modelLoanOptionTerm->load(Yii::$app->request->post())){
        	$modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        	$modelLoanOptionTerm->association = $modelCurrentUser->association;
        	$modelLoanOptionTerm->save();
            $currentAction = "VIEW";
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentAction' => $currentAction,
            'modelLoanOptionTerm' => $modelLoanOptionTerm,
        ]);
    }

    /**
     * Creates a new LoanOption model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LoanOption();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $model->association = $modelCurrentUser->association;
        $model->refund_deadline = 100;

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->isGivenIntervalOk() && $model->save()){
                    $model->initiallizeTerms();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LoanOption model.
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
     * Deletes an existing LoanOption model.
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
     * Finds the LoanOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LoanOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LoanOption::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
    * Get refund day rank based on user choosen refund day type
    */

    public function actionGetListOfRefundDayRanks($refundDayType) {
        $refunDayRanks = array(
            0 => Yii::t('app', 'Monday'),
            1 => Yii::t('app', 'Tuesday'),
        );
        echo Json::encode([
            'refunDayRanks' => $refunDayRanks,
        ]);
    }
}

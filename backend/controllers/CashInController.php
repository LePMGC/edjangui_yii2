<?php

namespace backend\controllers;

use common\models\CashIn;
use common\models\User;
use common\models\Episode;
use common\models\Account;
use common\models\CashInSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\filters\AccessControl;

/**
 * CashInController implements the CRUD actions for CashIn model.
 */
class CashInController extends Controller
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
     * Lists all CashIn models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CashInSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CashIn model.
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
     * Creates a new CashIn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CashIn();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $model->association = $modelCurrentUser->association;
        $model->episode = Episode::getCurrentEpisode();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                if($model->validate()){
                    //Update the bank abount of the user                
                    $modelAccount = Account::findOne(['owner' => $model->member, 'bank_account' => $model->bank_account, 'association' => $modelCurrentUser->association]);

                    //create the account for the user if it doesnt exist
                    if(is_null($modelAccount)){
                        $modelAccount = new Account();
                        $modelAccount->owner = $model->member;
                        $modelAccount->bank_account = $model->bank_account;
                        $modelAccount->association = $model->association;
                        $modelAccount->balance = 0;
                        $modelAccount->save();
                    }

                    $model->balance_before = $modelAccount->balance;
                    $modelAccount->balance = $modelAccount->balance + $model->amount;
                    $modelAccount->save();

                    $model->balance_after = $model->balance_before + $model->amount;
                    $model->save();

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
     * Updates an existing CashIn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->isLastOperationOnTheAccount()){

            if ($this->request->isPost && $model->load($this->request->post())) {
                //Update Account balance
                $bank_account = CashIn::findOne($id)->bank_account;
                $deltaBalance = $model->balance_after - $model->balance_before;
                $modelAccount = Account::findOne(['owner' => $model->member, 'bank_account' => $bank_account]);
                $modelAccount->balance = $modelAccount->balance + ($model->amount - $deltaBalance);
                $modelAccount->save();

                //Update balance after
                $model->balance_after = $model->balance_before + $model->amount;
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);

        }else{
            Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::t('app', 'This Operation cannot be updated, as another operation has already been done on the member account').'</font> </center>');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    /**
     * Deletes an existing CashIn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $modelCashIn = CashIn::findOne($id);
        if($modelCashIn->isLastOperationOnTheAccount()){
            //Update Account Balance    
            $modelAccount = Account::findOne($modelCashIn->member);
            $modelAccount->balance = $modelAccount->balance - ($modelCashIn->balance_after - $modelCashIn->balance_before);
            $modelAccount->save();

            $modelCashIn->delete();
            return $this->redirect(['index']);
         }else{
            Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::t('app', 'This Operation cannot be deleted, as another operation has already been done on the member account').'</font> </center>');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    /**
     * Finds the CashIn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CashIn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CashIn::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

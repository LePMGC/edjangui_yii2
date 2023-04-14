<?php

namespace backend\controllers;

use common\models\Loan;
use common\models\User;
use common\models\CashIn;
use common\models\Account;
use common\models\LoanEndorse;
use common\models\LoanSearch;
use common\models\LoanRefund;
use common\models\LoanRefundSearch;
use common\models\Episode;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\LoanInterestShareSearch;
use common\models\LoanInterestShare;
use Yii;
use yii\filters\AccessControl;
/**
 * LoanController implements the CRUD actions for Loan model.
 */
class LoanController extends Controller
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
                            'actions' => ['index', 'view', 'update', 'delete', 'create', 'process-loan-request'],
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
     * Lists all Loan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LoanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Loan model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $currentAction="VIEW", $loanRefundId=0)
    {
        $modelLoanEndorser = LoanEndorse::find()->where(['loan' => $id])->one();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $modelInterestShareSearch = new LoanInterestShareSearch();
        $modelInterestShareSearch->loan = $id;
        $dataProviderInterestShare = $modelInterestShareSearch->search([]);

        $modelLoanRefund = new LoanRefund();
        $modelLoanRefundSearch = new LoanRefundSearch();
        $modelLoanRefundSearch->loan = $id;
        $dataProviderLoanRefunds = $modelLoanRefundSearch->search([]);

        if($currentAction=="UPDATE") 
            $modelLoanRefund = LoanRefund::findOne($loanRefundId);
        elseif($currentAction=="DELETE"){
            $modelLoanRefund = LoanRefund::findOne($loanRefundId);
            $modelLoanRefund->delete();
            $modelLoanRefund = new LoanRefund();
        }else{
            $modelLoanRefund = new LoanRefund();
            $modelLoanRefund->remain_before = Loan::findOne($id)->getRemainingAmountToRefund();
            $modelLoanRefund->loan = $id;
        }

        if ($modelLoanRefund->load(Yii::$app->request->post())){
            $modelLoanRefund->refund_date = date('Y-m-d', strtotime($modelLoanRefund->refund_date));
            $modelLoanRefund->remain_after = $modelLoanRefund->remain_before - $modelLoanRefund->amount_given;
            $modelLoanRefund->association = $modelCurrentUser->association;
            $modelLoanRefund->save();
            $modelLoanRefund->computeInterestsSharingAndNotifyMembers();
            $modelLoanRefund->notifyTakerThatRefundIsSaved();
            $currentAction = "VIEW";
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelLoanEndorser' => $modelLoanEndorser,
            'searchModel' => $modelInterestShareSearch,
            'dataProviderInterestShare' => $dataProviderInterestShare,
            'dataProviderLoanRefunds' => $dataProviderLoanRefunds,
            'modelLoanRefund' => $modelLoanRefund,
            'currentAction' => $currentAction,
        ]);
    }

    /**
     * Creates a new Loan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Loan();

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
     * Updates an existing Loan model.
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
     * Deletes an existing Loan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteTheLoan();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loan::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
    * This method will be used once the loan request have been approved by the endorser and the admin has give (or is ready to give) money to the loan taker/requestor.
    * The process will consist of : changing the loan status, create interest shares for all the members, send notification to the taker
    * @param integer $id
    * @return mixed
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionProcessLoanRequest($id){
        //Create the interest share for all the members
        $model = $this->findModel($id);

        //check if the user had another loan before requesting this one
        $modelFormerLoan = Loan::find()
            ->where('taker = '.$model->taker.' and status = '.Loan::LOAN_GIVEN.' and id < '.$model->id)
            ->one();
        if(!is_null($modelFormerLoan)){
            $modelLoanRefund = new LoanRefund();
            $modelLoanRefund->amount_given = $modelFormerLoan->getRemainingAmountToRefund();
            $modelLoanRefund->remain_before = $modelLoanRefund->amount_given;
            $modelLoanRefund->remain_after = 0;
            $modelLoanRefund->loan = $modelFormerLoan->id;
            $modelLoanRefund->refund_date = Date('Y-m-d');
            if($modelLoanRefund->save()){
                $modelFormerLoan->status = Loan::LOAN_RETURNED;
                $modelFormerLoan->save();
                $modelLoanRefund->computeInterestsSharingAndNotifyMembers();
            }
        }

        $model->initializeInterestsAndShare();

        //send noticiation to loan taker
        $model->notifyTakerThatLoanIsProcessed();

        //change the loan status
        $model->status = Loan::LOAN_GIVEN;
        if($model->save()){
            //check if there is a need to fill up the member bank account so that to reach the required minimum
            if($model->amount_to_cash_in > 0){
                $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
                $modelAccount = Account::findOne(['owner' => $model->taker, 'bank_account' => $model->bank_account]);
                $modelEpisode = Episode::find()->where(['association' => $modelCurrentUser->association])
                                                ->andWhere("start_date <= '".date('Y-m-d')."' and '".date('Y-m-d')."' <= end_date")
                                                ->one();

                $modelCashIn = new CashIn();
                $modelCashIn->member = $model->taker;
                $modelCashIn->bank_account = $model->bank_account;
                $modelCashIn->balance_before = $modelAccount->balance;
                $modelCashIn->amount = $model->amount_to_cash_in;
                $modelCashIn->balance_after = $modelCashIn->balance_before + $modelCashIn->balance_after;
                $modelCashIn->association = $modelCurrentUser->association;
                $modelCashIn->episode = $modelEpisode->id;
                $modelCashIn->save();

                $modelAccount->balance += $model->amount_to_cash_in;
                $modelAccount->save();

                
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }
}

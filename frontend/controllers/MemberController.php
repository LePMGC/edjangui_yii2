<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\models\LoanSearch;
use common\models\Loan;
use common\models\Account;
use common\models\BankAccount;
use common\models\DjanguiSeason;
use common\models\LoanEndorse;
use app\models\AccountHistory;
use app\models\CashOutForm;
use app\models\LoanForm;
use app\models\LoanEndoserSearch;
use common\models\LoanEndorseComment;
use yii\helpers\Json;

/**
 * Member controller
 */
class MemberController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['edit-profile', 'account-history-view-more', 'account-history-detailled', 'loans-view-more', 'loans-detailled', 'cash-out', 'view-ah-item', 'ask-a-loan', 'approve-endorse-request', 'reject-endorse-request', 'view-a-loan', 'edit-a-loan', 'delete-a-loan'],
                'rules' => [
                    [
                        'actions' => ['edit-profile', 'account-history-view-more', 'account-history-detailled', 'loans-view-more', 'loans-detailled', 'view-ah-item', 'approve-endorse-request', 'reject-endorse-request', 'edit-a-loan'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['cash-out'],
                        'allow' => CashOutForm::currentUserCanCashOut(),
                        'roles' => ['@'],
                        'denyCallback'  => function ($rule, $action) {
                            Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::t('app', 'You can\'t request for a Cash Out now. You already have another one pending.').'</font> </center>');
                            $this->redirect(['site/index']);
                        },
                    ],
                    [
                        'actions' => ['ask-a-loan'],
                        'allow' => !Yii::$app->user->getIsGuest() && LoanForm::currentUserCanLoan(),
                        'roles' => ['@'],
                        'denyCallback'  => function ($rule, $action) {
                            Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::$app->session->get('currentUserCantLoanReason').'</font> </center>');
                            $this->redirect(['site/index']);
                        },
                    ],
                    [
                        'actions' => ['view-a-loan', 'delete-a-loan'],                        
                        'allow' => !is_null(Loan::find()->where(['id' => (isset(Yii::$app->request->queryParams['loanId']) ? Yii::$app->request->queryParams['loanId'] : 0), 'taker' => Yii::$app->user->getId()])->one()),
                        'roles' => ['@'],
                        'denyCallback'  => function ($rule, $action) {
                            Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::t('app', 'You are not the requestor of this Loan').'</font> </center>');
                            $this->redirect(['site/index']);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cash-out' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Edit a member profile.
     *
     * @return mixed
     */
    public function actionEditProfile(){
        $member = Yii::$app->user->getId();
        
        $model = User::findOne($member);

        if ($model->load(Yii::$app->request->post()) ) {
            $model->name = $_POST['User']['name'];
            $model->phone_number = $_POST['User']['phone_number'];
            $model->email_address = $_POST['User']['email_address'];
            $model->username = $_POST['User']['username'];
            if($model->save())
                return $this->redirect(['site/index']);
        }

        return $this->render('edit-profile', [
            'model' => $model,
        ]);
    }

    /**
     * Account History View More.
     * @param integer
     * @return mixed
     */
    public function actionAccountHistoryViewMore(){
        $member=Yii::$app->user->getId();
        
        $model = User::findOne($member);

        return $this->render('account-history-view-more', [
            'model' => $model,
        ]);
    }

    /**
     * Account History View Detailled.
     *
     * @return mixed
     */
    public function actionAccountHistoryDetailled(){
        $member=Yii::$app->user->getId();
        
        $searchModel = new AccountHistory();
        $searchModel->member = $member;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('account-history-detailled', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Loans View More.
     * @param integer
     * @return mixed
     */
    public function actionLoansViewMore(){
        $member=Yii::$app->user->getId();
        
        $model = User::findOne($member);

        return $this->render('loans-view-more', [
            'model' => $model,
        ]);
    }

    /**
     * Loans Detailled.
     * @return mixed
     */
    public function actionLoansDetailled(){
        $member=Yii::$app->user->getId();
        
        $searchModel = new LoanSearch();
        //$searchModel->taker = $member;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('loans-detailled', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Cash Out.
     * @return mixed
     */
    public function actionCashOut(){
        $member=Yii::$app->user->getId();

        $model = new CashOutForm();
        $modelUser = User::findOne($member);
        $model->phone_number = $modelUser->phone_number;
        $model->payment_method = 1;
        $model->member = $member;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->performCashOutRequest()){
                Yii::$app->session->setFlash('success', '<center>'.Yii::t('app', 'Your request have been received well. You will be contacted soon to complete the Cash Out operation').'</center>');
                return $this->redirect(['site/index']);
            }
        }

        return $this->render('cash-out', [
            'model' => $model,
        ]);
    }

    /**
    * Get details about an item of Account History to display in modal
    * @return mixed
    */
    public function actionViewAhItem($ah_key){
        return $this->renderAjax('view-ah-item', ['ah_key' => $ah_key]);
    }


    /**
     * Ask a loan.
     * @return mixed
     */
    public function actionAskALoan($action='NEW'){
        $member = Yii::$app->user->getId();
        $modelUser = User::findOne($member);

        $session = Yii::$app->session;
        $session->open();

        $model = new LoanForm();
        if(strcmp($action, 'NEW') == 0){
            $model->taker = $member;
            $model->phone_number = $modelUser->phone_number;
            $model->payment_method = 1;
            $model->bank_account = BankAccount::getDetaultBankAccountIdForLoan();
        }else{            
            $model->taker = isset($_SESSION['loan_taker']) ? $_SESSION['loan_taker'] : '';
            $model->amount = isset($_SESSION['loan_amount']) ? $_SESSION['loan_amount'] : '';
            $model->amount_taken = isset($_SESSION['loan_amount_taken']) ? $_SESSION['loan_amount_taken'] : '';
            $model->amount_to_cash_in = isset($_SESSION['amount_to_cash_in']) ? $_SESSION['amount_to_cash_in'] : '';
            $model->start_date = isset($_SESSION['loan_start_date']) ? $_SESSION['loan_start_date'] : '';
            $model->end_date = isset($_SESSION['loan_end_date']) ? $_SESSION['loan_end_date'] : '';
            $model->interest = isset($_SESSION['loan_interest']) ? $_SESSION['loan_interest'] : '';
            $model->phone_number = isset($_SESSION['loan_phone_number']) ? $_SESSION['loan_phone_number'] : '';
            $model->payment_method = isset($_SESSION['loan_payment_method']) ? $_SESSION['loan_payment_method'] : '';
            $model->endorser = isset($_SESSION['loan_endorser']) ? $_SESSION['loan_endorser'] : '';
            $model->bank_account = isset($_SESSION['bank_account']) ? $_SESSION['bank_account'] : '';
        }        

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $model->generateLoanDetails();
                
                //save loan request data in session
                $session->set('loan_taker', $model->taker);
                $session->set('loan_amount', $model->amount);
                $session->set('loan_amount_taken', $model->amount_taken);
                $session->set('amount_to_cash_in', $model->amount_to_cash_in);
                $session->set('loan_interest', $model->interest);
                $session->set('loan_start_date', $model->start_date);
                $session->set('loan_end_date', $model->end_date);
                $session->set('loan_phone_number', $model->phone_number);
                $session->set('loan_payment_method', $model->payment_method);
                $session->set('loan_endorser', $model->endorser);
                $session->set('bank_account', $model->bank_account);

                if($model->former_loan_amount_to_refund > 0){
                    Yii::$app->session->setFlash('success', '<center>'.Yii::t('app', 'As you already have a non refunded loan the loan amount is increased with the balance of the previous loan.').'</center>');
                }

                return $this->redirect(['confirm-loan-request', 'isItANewLoan' => true]);
            }
        }

        return $this->render('ask-a-loan', [
            'model' => $model,
        ]);
    }




    /**
    *
    * @return mixed
    */
    public function actionApproveEndorseRequest($endorseId){
        $modelLoanEndose = LoanEndorse::findOne($endorseId);
        if($modelLoanEndose->status == LoanEndorse::LOAN_ENDORSE_REQUESTED){
            if($modelLoanEndose->endorser == Yii::$app->user->getId()) {
                $modelLoanEndose->status = LoanEndorse::LOAN_ENDORSE_APPROVED;
                $modelLoanEndose->save();
                $loan_taker_name = User::findOne(['id' => Loan::findOne(['id' => $modelLoanEndose->loan])->taker])->name;

                //Send notification to the loan requested.
                $modelLoanEndose->sendNotificationToLoanTakerOnApprovalOfEndorseRequest();

                Yii::$app->session->setFlash('success', '<center> <font style="font-size:15px">'.Yii::t('app', 'Status of the endorse request have been changed to APPROVED and').' '.$loan_taker_name.' '.Yii::t('app', 'is notified as well').'.</font> </center>');
            }else{
                Yii::$app->session->setFlash('danger', '<center> <font style="font-size:15px">'.Yii::t('app', 'You are not the owner of this Loan Endorse Request').'</font> </center>');
            }
        }else{
            Yii::$app->session->setFlash('danger', '<center> <font style="font-size:15px">'. Yii::t('app', 'This Loan Endorse Request has already been approved.').'</font> </center>');
        }

        return $this->redirect(['site/index']);
    }

    /**
    *
    * @return mixed
    */
    public function actionRejectEndorseRequest($endorseId){
        $modelLoanEndose = LoanEndorse::findOne($endorseId);
        if($modelLoanEndose->status == LoanEndorse::LOAN_ENDORSE_REQUESTED){
            if($modelLoanEndose->endorser == Yii::$app->user->getId()) {
                
                $model = new LoanEndorseComment();
                $model->loan_endorse = $endorseId;
                $model->author = Yii::$app->user->getId();

                if ($model->load(Yii::$app->request->post())) {

                    $model->save();

                    $modelLoanEndose->status = LoanEndorse::LOAN_ENDORSE_REJECTED;
                    $modelLoanEndose->save();
                    $loan_taker_name = User::findOne(['id' => Loan::findOne(['id' => $modelLoanEndose->loan])->taker])->name;

                    //Send notification to the loan requested.
                    $modelLoanEndose->sendNotificationToLoanTakerOnRejectionOfEndorseRequest();

                    Yii::$app->session->setFlash('success', '<center> <font style="font-size:15px">'. Yii::t('app', 'Status of the endorse request have been changed to REJECTED and').' '.$loan_taker_name.' '.Yii::t('app', 'is notified as well').'.</font> </center>');
                }else
                    return $this->render('loan-endorse-rejection', [
                        'model' => $model,
                        'modelLoanEndose' => $modelLoanEndose
                    ]);

            }else{
                Yii::$app->session->setFlash('danger', '<center> <font style="font-size:15px">'.Yii::t('app', 'You are not the owner of this Loan Endorse Request').'</font> </center>');
            }
        }else{
            if($modelLoanEndose->status == LoanEndorse::LOAN_ENDORSE_APPROVED){
                Yii::$app->session->setFlash('danger', '<center> <font style="font-size:15px">'.Yii::t('app', 'This Loan Endorse Request has already been approved').'.</font> </center>');
            }else{
                Yii::$app->session->setFlash('danger', '<center> <font style="font-size:15px">'.Yii::t('app', 'This Loan Endorse Request has already been rejected').'.</font> </center>');
            }
        }

        return $this->redirect(['site/index']);
    }

    
    /**
    *
    * @return mixed
    */
    public function actionLoanEndorseRequests(){
        $memberId = Yii::$app->user->getId();
        $endorseRequestsData = LoanEndorse::getEndorseRequestsData($memberId);

        return $this->render('loan-endorse-requests', [
            'endorseRequestsData' => $endorseRequestsData,
        ]);
    }


    /**
    *
    * @return mixed
    */
    public function actionViewALoan($loanId){
        $memberId = Yii::$app->user->getId();
        $modelLoan = Loan::findOne($loanId);
        $modelLoanEndoserSearch = new LoanEndoserSearch();

        return $this->render('view-a-loan', [
            'modelLoan' => $modelLoan,
            'endorsersData' => $modelLoanEndoserSearch->getLoanEndosersData($loanId),
            'refundsData' => $modelLoan->getLoanRefundsData(),
        ]);
    }


    /**
    *
    * @return mixed
    */
    public function actionDeleteALoan($loanId){
        $modelLoan = Loan::findOne($loanId);

        if($modelLoan->deleteTheLoan()){
            Yii::$app->session->setFlash('success', '<center>'.Yii::t('app', 'The Loan has been deleted successfully').'</center>');
            return $this->redirect(['site/index']);
        }
    }

    /**
    *
    * @return mixed
    */
    public function actionEditALoan($loanId, $action='NEW'){
        $modelLoan = Loan::findOne($loanId);

        $member = Yii::$app->user->getId();
        $modelUser = User::findOne($member);

        $session = Yii::$app->session;
        $session->open();

        $model = new LoanForm();
        $model->loan_id = $loanId;
        $model->taker = $modelLoan->taker;
        $model->phone_number = $modelLoan->phone_number;
        $model->payment_method = $modelLoan->payment_method;
        $model->amount = isset($_SESSION['loan_amount']) ? $_SESSION['loan_amount'] : $modelLoan->amount_requested;
        $model->bank_account = $modelLoan->bank_account;
        $model->endorser = $model->getLastEndorserId();

        if(strcmp($action, 'NEW') == 0){
            /*$model->taker = $member;
            $model->phone_number = $modelUser->phone_number;
            $model->payment_method = 1;*/
        }else{
            $model->loan_id = isset($_SESSION['loan_id']) ? $_SESSION['loan_id'] : '';
            $model->taker = isset($_SESSION['loan_taker']) ? $_SESSION['loan_taker'] : '';
            $model->amount = isset($_SESSION['loan_amount']) ? $_SESSION['loan_amount'] : '';
            $model->amount_taken = isset($_SESSION['loan_amount_taken']) ? $_SESSION['loan_amount_taken'] : '';
            $model->amount_to_cash_in = isset($_SESSION['amount_to_cash_in']) ? $_SESSION['amount_to_cash_in'] : '';
            $model->start_date = isset($_SESSION['loan_start_date']) ? $_SESSION['loan_start_date'] : '';
            $model->end_date = isset($_SESSION['loan_end_date']) ? $_SESSION['loan_end_date'] : '';
            $model->interest = isset($_SESSION['loan_interest']) ? $_SESSION['loan_interest'] : '';
            $model->phone_number = isset($_SESSION['loan_phone_number']) ? $_SESSION['loan_phone_number'] : '';
            $model->payment_method = isset($_SESSION['loan_payment_method']) ? $_SESSION['loan_payment_method'] : '';
            $model->endorser = isset($_SESSION['loan_endorser']) ? $_SESSION['loan_endorser'] : '';
            $model->bank_account = isset($_SESSION['bank_account']) ? $_SESSION['bank_account'] : '';
        }        

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $model->generateLoanDetails();
                
                //save loan request data in session
                $session->set('loan_id', $model->loan_id);
                $session->set('loan_taker', $model->taker);
                $session->set('loan_amount', $model->amount);
                $session->set('loan_amount_taken', $model->amount_taken);
                $session->set('amount_to_cash_in', $model->amount_to_cash_in);
                $session->set('loan_interest', $model->interest);
                $session->set('loan_start_date', $model->start_date);
                $session->set('loan_end_date', $model->end_date);
                $session->set('loan_phone_number', $model->phone_number);
                $session->set('loan_payment_method', $model->payment_method);
                $session->set('loan_endorser', $model->endorser);
                $session->set('bank_account', $model->bank_account);

                return $this->redirect(['confirm-loan-request', 'isItANewLoan' => false]);
            }
        }


        return $this->render('edit-a-loan', [
            'model' => $model,
            'amountAvailableForLoan' => Loan::getTotalAmountAvailableForLoan() + $modelLoan->amount_requested,
            'loanId' => $loanId,
        ]);
    }

    /**
    *
    * @return mixed
    */
    public function actionConfirmLoanRequest($isItANewLoan=true){

        //Get loan data from session
        $modelLoanForm = new LoanForm();
        $modelLoanForm->loan_id = isset($_SESSION['loan_id']) ? $_SESSION['loan_id'] : '';
        $modelLoanForm->taker = isset($_SESSION['loan_taker']) ? $_SESSION['loan_taker'] : '';
        $modelLoanForm->amount = isset($_SESSION['loan_amount']) ? $_SESSION['loan_amount'] : '';
        $modelLoanForm->amount_taken = isset($_SESSION['loan_amount_taken']) ? $_SESSION['loan_amount_taken'] : '';
        $modelLoanForm->amount_to_cash_in = isset($_SESSION['amount_to_cash_in']) ? $_SESSION['amount_to_cash_in'] : '';
        $modelLoanForm->start_date = isset($_SESSION['loan_start_date']) ? $_SESSION['loan_start_date'] : '';
        $modelLoanForm->end_date = isset($_SESSION['loan_end_date']) ? $_SESSION['loan_end_date'] : '';
        $modelLoanForm->interest = isset($_SESSION['loan_interest']) ? $_SESSION['loan_interest'] : '';
        $modelLoanForm->phone_number = isset($_SESSION['loan_phone_number']) ? $_SESSION['loan_phone_number'] : '';
        $modelLoanForm->payment_method = isset($_SESSION['loan_payment_method']) ? $_SESSION['loan_payment_method'] : '';
        $modelLoanForm->endorser = isset($_SESSION['loan_endorser']) ? $_SESSION['loan_endorser'] : '';
        $modelLoanForm->bank_account = isset($_SESSION['bank_account']) ? $_SESSION['bank_account'] : '';

        if(isset($_POST['confirm-button'])){
            //save the loan and send notifications
            
            if($modelLoanForm->performLoanRequest($isItANewLoan))
                Yii::$app->session->setFlash('success', '<center>'.Yii::t('app', 'Your request have been received well. You will be contacted soon to complete the Loan operation').'</center>');
            else{
                Yii::$app->session->setFlash('danger', '<center>'.Yii::t('app', 'There was a problem when processing your loan request. Please try again later or contact the system administrator').'</center>');
            }

            return $this->redirect(['site/index']);
        }else

        return $this->render('confirm-loan', [
            'modelLoanForm' => $modelLoanForm,
        ]);
    }


    /**
    *
    * @return mixed
    */
    public function actionConfirmUpdateLoanRequest(){

        //Get loan data from session
        $modelLoanForm = new LoanForm();
        $modelLoanForm->loan_id = isset($_SESSION['loan_id']) ? $_SESSION['loan_id'] : '';
        $modelLoanForm->taker = isset($_SESSION['loan_taker']) ? $_SESSION['loan_taker'] : '';
        $modelLoanForm->amount = isset($_SESSION['loan_amount']) ? $_SESSION['loan_amount'] : '';
        $modelLoanForm->amount_taken = isset($_SESSION['loan_amount_taken']) ? $_SESSION['loan_amount_taken'] : '';
        $model->amount_to_cash_in = isset($_SESSION['amount_to_cash_in']) ? $_SESSION['amount_to_cash_in'] : '';
        $modelLoanForm->start_date = isset($_SESSION['loan_start_date']) ? $_SESSION['loan_start_date'] : '';
        $modelLoanForm->end_date = isset($_SESSION['loan_end_date']) ? $_SESSION['loan_end_date'] : '';
        $modelLoanForm->interest = isset($_SESSION['loan_interest']) ? $_SESSION['loan_interest'] : '';
        $modelLoanForm->phone_number = isset($_SESSION['loan_phone_number']) ? $_SESSION['loan_phone_number'] : '';
        $modelLoanForm->payment_method = isset($_SESSION['loan_payment_method']) ? $_SESSION['loan_payment_method'] : '';
        $modelLoanForm->endorser = isset($_SESSION['loan_endorser']) ? $_SESSION['loan_endorser'] : '';

        $currentUser = User::findOne(Yii::$app->user->getId());
        $modelLoanOption = LoanOption::find()
            ->where([
                'association' => $currentUser->association, 
                'bank_account' => $modelLoanForm->bank_account,
            ])
            ->andWhere('min_amount <= '.$modelLoanForm->amount.' and '.$modelLoanForm->amount.' <= max_amount')
            ->one();

        if(isset($_POST['confirm-button'])){
            //save the loan and send notifications
            $modelLoanForm->updateLoanRequest();
            Yii::$app->session->setFlash('success', '<center>'.Yii::t('app', 'Your request have been received well. You will be contacted soon to complete the Loan operation').'</center>');
            return $this->redirect(['site/index']);
        }


        return $this->render('confirm-loan', [
            'modelLoanForm' => $modelLoanForm,
        ]);
    }
    

    /**
    *
    * @return mixed
    */
    public function actionGetEndosersEditLoan($loanId){
        $modelLoanEndoserSearch = new LoanEndoserSearch();
        if(Yii::$app->session->get('endorsersData') == null)
            $endorsersData = $modelLoanEndoserSearch->getEndosersDataEditLoan($loanId);
        else
            $endorsersData = Yii::$app->session->get('endorsersData');

        if(isset($_POST)){
            //Collect selected endorsed amount and keep in table models
            $postArrayKeys = array_keys($_POST);
            foreach ($postArrayKeys as $postArrayKey) {
                if(strcmp($postArrayKey, "user") > 0){
                    $t = explode("-", $postArrayKey);
                    $endorsersData[$t[1]]['requested_endorse_amount'] = $_POST[$postArrayKey];
                }
            }
            Yii::$app->session->set('endorsersData', $endorsersData);

            //if sum of requested amount is less than the amount to be endorsed, send user back to form.
            if(isset($_POST['sumRequestedAmounts'])){
                $sumRequestedAmounts = $_POST['sumRequestedAmounts'];
                if($_POST['sumRequestedAmounts'] < Yii::$app->session->get('required_endorse_amount')){
                    Yii::$app->session->setFlash('error', '<center> <font style="font-size:15px">'.Yii::t('app', 'The amount requested'). ' ('.Yii::$app->formatter->asDecimal($sumRequestedAmounts).') '.Yii::t('app', 'is still less than the amount you need').' ('.Yii::$app->formatter->asDecimal(Yii::$app->session->get('required_endorse_amount')).'). '.Yii::t('app', 'Kindly request more.').'</font> </center>');
                }
                else{
                    Yii::$app->session->set('endorsersData', $endorsersData);
                    return $this->redirect(['confirm-edit-loan-with-endorsers', 'loanId' => $loanId]);
                }
            }
        }

        return $this->render('get-endosers-edit-loan', [
            'loan_amount' => Yii::$app->session->get('loan_amount'),
            'required_endorse_amount' => Yii::$app->session->get('required_endorse_amount'),
            'endorsersData' => $endorsersData,
            'loanId' => $loanId,
        ]);
    }

    /**
    * set the choosen bank account in session- data
    */
    public function actionSetAccountHistoryBankAccount($bankAccount){
        $session = Yii::$app->session;
        $session->open();
        $session->set('account_history_bank_account', $bankAccount);
        return $this->redirect(['site/index']);
    }


    /**
    * set the choosen djangui in session- data
    */
    public function actionSetMembersContributionsCurrentDjangui($djangui){
        $session = Yii::$app->session;
        $session->open();
        $session->set('members_contributions_current_djangui', $djangui);
        return $this->redirect(['site/index']);
    }


    public function actionGetMaxAllowedLoanAmount($bankAccountId) {
        $modelBankAccount = BankAccount::findOne($bankAccountId);

        echo Json::encode([
            'max_allowed_loan_amount' => $modelBankAccount->getTotalAmountAvailableForLoan(),
        ]);  
    }
}
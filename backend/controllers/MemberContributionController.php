<?php

namespace backend\controllers;

use Yii;
use common\models\CashIn;
use common\models\CashInSearch;
use common\models\Season;
use common\models\DjanguiMember;
use common\models\Account;
use common\models\Parameter;
use common\models\Episode;
use common\models\EpisodeSearch;
use common\models\DjanguiContribution;
use common\models\BankAccount;
use common\models\Djangui;
use app\models\MemberContribution;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use common\models\User;

/**
 * MemberContributionController implements the CRUD actions for CashIn model.
 */
class MemberContributionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(){
        return [
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
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CashIn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MemberContribution();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->getMemberContributionsData($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    /**
     * Displays a single MemberContribution model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Finds the MemberContribution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MemberContribution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MemberContribution::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

     /**
     * Creates a new MemberContribution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MemberContribution();
        $model->episode = Episode::getCurrentEpisode();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        //$load djangui contributions fileds;
        $model->djangui_contributions = array();
        $modelDjanguis = Djangui::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
        $i = 0;
        foreach ($modelDjanguis as $modelDjangui) {
            $model->djangui_contributions[$i++] = array(
                'djangui_name' => $modelDjangui->name,
                'djangui' => $modelDjangui->id,
                'djangui_contribution' => 0,
                'djangui_contribution_id' => 0,
            );
        }

        //$load bank contributions fileds;
        $model->bank_contributions = array();
        $modelBankAccounts = BankAccount::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
        $i = 0;
        foreach ($modelBankAccounts as $modelBankAccount) {
            $model->bank_contributions[$i++] = array(
                'bank_account_name' => $modelBankAccount->name,
                'bank_account' => $modelBankAccount->id,
                'bank_contribution' => 0,
                'cash_in_id' => 0,
            );
        }



        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            //djangui contributions
            $modelDjanguiContribution = DjanguiContribution::find()->where(['episode' => $model->episode, 'member' => $model->member, 'association' => $modelCurrentUser->association])->one();
            $modelDjanguis = Djangui::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
            $modelsSaved = true;
            foreach ($modelDjanguis as $modelDjangui) {
                if(isset($_POST['djangui_contribution_'.$modelDjangui->id])){
                    $modelDjanguiContribution = DjanguiContribution::findOne(['episode' => $model->episode, 'member' => $model->member, 'djangui' => $modelDjangui->id]);
                    if(is_null($modelDjanguiContribution)){
                        $modelDjanguiContribution = new DjanguiContribution();
                        $modelDjanguiContribution->member = $model->member;
                        $modelDjanguiContribution->djangui = $modelDjangui->id;
                        $modelDjanguiContribution->episode = $model->episode;
                        $modelDjanguiContribution->association = $modelCurrentUser->association;
                        $modelDjanguiContribution->expected_amount = $modelDjangui->amount;
                    }
                    $modelDjanguiContribution->contribution_amount = $_POST['djangui_contribution_'.$modelDjangui->id];
                    $modelDjanguiContribution->save();
                    
                    if($modelDjanguiContribution->save())
                        $modelsSaved = $modelsSaved & true;
                    else{
                        $modelsSaved = $modelsSaved & false;    
                        print_r($modelDjanguiContribution->getErrors());
                    }
                }
            }

            //$modelsSaved = true;
            //save bank contributions
            $modelBankAccounts = BankAccount::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
            foreach ($modelBankAccounts as $modelBankAccount) {
                if(isset($_POST['bank_contribution_'.$modelBankAccount->id]) && $_POST['bank_contribution_'.$modelBankAccount->id] >0){
                    $modelCashIn = new CashIn();
                    $modelCashIn->member = $model->member;
                    $modelCashIn->bank_account = $modelBankAccount->id;
                    $modelCashIn->episode = $model->episode;
                    $modelCashIn->amount = $_POST['bank_contribution_'.$modelBankAccount->id];
                    $modelAccount = Account::find()->where([
                                        'owner' => $model->member,
                                        'bank_account' => $modelBankAccount->id,
                                        'association' => $modelCurrentUser->association
                                    ])->one();
                    //if the user is not having an account yet, create one for him
                    if(is_null($modelAccount)){
                        $modelAccount = new Account();
                        $modelAccount->owner = $model->member;
                        $modelAccount->bank_account = $modelBankAccount->id;
                        $modelAccount->balance = 0;
                        $modelAccount->association = $modelCurrentUser->association;
                        $modelAccount->save();
                    }

                    $modelCashIn->balance_before = $modelAccount->balance;
                    $modelAccount->balance += $modelCashIn->amount;
                    $modelCashIn->balance_after = $modelAccount->balance;
                    $modelCashIn->association = $modelCurrentUser->association;
                    if($modelAccount->save() && $modelCashIn->save())
                        $modelsSaved = $modelsSaved & true;
                    else{
                        $modelsSaved = $modelsSaved & false;
                        print_r($modelAccount->getErrors());    
                        print_r($modelCashIn->getErrors());
                    }
                }
            }
            

            if ($modelsSaved)
                return $this->redirect(['view', 'id' => $model->episode."_".$model->member]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing CashIn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            //djangui contribution
            $modelDjanguiContribution = DjanguiContribution::find()->where(['episode' => $model->episode, 
                                                                            'user' => $model->member, 
                                                                            'association' => $modelCurrentUser->association])->one();
            $modelDjanguiContribution->episode = $model->episode;
            $modelDjanguiContribution->user = $model->member;
            $modelDjanguiContribution->contribution_amount = $_POST['MemberContribution']['djangui_contribution'];
            $modelDjanguiContribution->expected_amount = $_POST['expected_djangui_contribution_amount'];

            if($modelDjanguiContribution->save()){
                $modelsSaved = true;
                //bank contributions
                $modelBankAccounts = BankAccount::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
                foreach ($modelBankAccounts as $modelBankAccount) {
                    if(isset($_POST['bank_contribution_'.$modelBankAccount->id])){
                        $modelCashIn = CashIn::find()->where(['episode' => $model->episode, 'bank_account' => $modelBankAccount->id, 'user' => $model->member])->one();
                        $modelCashIn->user = $model->member;
                        $modelCashIn->bank_account = $modelBankAccount->id;
                        $modelCashIn->episode = $model->episode;
                        $deltaAccountBalance = $_POST['bank_contribution_'.$modelBankAccount->id] - $modelCashIn->transaction_amount;
                        $modelCashIn->transaction_amount = $_POST['bank_contribution_'.$modelBankAccount->id];
                        $modelAccount = Account::find()->where(['owner' => $model->member, 'bank_account' => $modelBankAccount->id])->one();
                        //$modelCashIn->balance_before = $modelAccount->balance;
                        $modelAccount->balance += $deltaAccountBalance;
                        $modelCashIn->balance_after = $modelAccount->balance;
                        if($modelAccount->save() && $modelCashIn->save())
                            $modelsSaved = $modelsSaved & true;
                        else{
                            $modelsSaved = $modelsSaved & false;
                            print_r($modelAccount->getErrors());    
                            print_r($modelCashIn->getErrors());
                        }
                    }
                }
            }else print_r($modelDjanguiContribution->getErrors());

            if ($modelsSaved)
                return $this->redirect(['view', 'id' => $model->episode."_".$model->member]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
}

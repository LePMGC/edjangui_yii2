<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\User;
use common\models\Association;
use common\models\LoanOption;
use common\models\LoanOptionTerm;
use common\models\BankAccount;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\models\LoanOption $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="loan-option-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    [ 
                        'label' => $model->getAttributeLabel('bank_account'),
                        'value' => BankAccount::findOne($model->bank_account)->name,
                    ],
                    'min_amount',
                    'max_amount',
                    'interest_rate',
                    'number_of_terms',
                    [ 
                        'label' => $model->getAttributeLabel('term_duration'),
                        'value' => LoanOption::getListOfAllTermDurations()[$model->term_duration],
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('postpone_option'),
                        'value' => LoanOption::getListOfAllPostponeOptions()[$model->postpone_option],
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('postpone_capital'),
                        'value' => LoanOption::getListOfAllPostponeCapital()[$model->postpone_capital],
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('association'),
                        'value' => Association::findOne($model->association)->name,
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('refund_deadline'),
                        'value' => LoanOption::getNameOfRedundDeadline($model->refund_deadline),
                    ],
                    [ 
                                                'label' => $model->getAttributeLabel('created_by'),
                                                'value' => User::findOne($model->created_by)->name,
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('created_on'),
                                                'value' => Yii::$app->formatter->asDateTime($model->created_on),
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('updated_by'),
                                                'value' => User::findOne($model->updated_by)->name,
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('updated_on'),
                                                'value' => Yii::$app->formatter->asDateTime($model->updated_on),
                                            ]
                ],
            ]) ?>
        </div>
        <div class="col-7">
            <center> <strong> <?= Yii::t('app', 'Terms') ?> </strong> </center>
             <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            
            <?php 
                if($currentAction == "VIEW"){                        
                    echo Html::a(Yii::t('app', '+Term'), ['loan-option/view', 'id' => $model->id, 'currentAction' => 'ADD'], ['class' => 'btn btn-primary']);
                }else{                          
                    echo Html::a(Yii::t('app', 'Terms'), ['loan-option/view', 'id' => $model->id, 'currentAction' => 'VIEW'], ['class' => 'btn btn-primary']);
                }
            ?>
            <br/>
            <br/>
            <?php

                if(($currentAction == "UPDATE") || ($currentAction == "ADD")){
                    $form = ActiveForm::begin();

                        echo $form->field($modelLoanOptionTerm, 'name')->textInput();

                        echo $form->field($modelLoanOptionTerm, 'rank')->textInput();

                        echo $form->field($modelLoanOptionTerm, 'amount_to_refund')->widget(Select2::classname(), [
                            'data' => array(0 => Yii::t('app', 'Capital'), 1 => Yii::t('app', 'Capital + Interests')),
                            'options' => [
                                'placeholder' => Yii::t('app', 'Select Amount').' ...',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]);

                        echo $form->field($modelLoanOptionTerm, 'percentage')->textInput();
                        
                        echo '<div class="form-group">';
                        echo Html::submitButton($modelLoanOptionTerm->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $modelLoanOptionTerm->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                        echo '</div>';
                    ActiveForm::end();

                }else{

                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'layout' => '{items}{pager}',
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],

                            //'id',
                            [
                                'attribute' => 'rank',
                                //'width' => '5%',
                            ],
                            'name',
                            [
                                'attribute' => 'amount_to_refund',
                                'value'=>function ($model, $key, $index, $widget) { 
                                    return $model->amount_to_refund == 0 ? Yii::t('app', 'Capital') : Yii::t('app', 'Capital + Interests'); 
                                },
                            ],
                            'percentage',
                            //'loan_option',
                            //'created_by',
                            //'created_on',
                            //'updated_by',
                            //'updated_on',
                            [
                                'class' => ActionColumn::className(),
                                'template' => '{update}{delete}',
                                'urlCreator' => function ($action, LoanOptionTerm $model, $key, $index, $column) {
                                    return Url::toRoute(['view', 'id' => $model->loan_option,'currentAction' =>strtoupper($action) , 'loanOptionTermId' => $model->id]);
                                 }
                            ],
                        ],
                    ]); 
                }
            ?>

    <?php Pjax::end(); ?>
        </div>
    </div>

</div>

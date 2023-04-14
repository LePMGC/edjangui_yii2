<?php

use common\models\LoanOption;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\BankAccount;
use yii\helpers\BaseUrl;
/** @var yii\web\View $this */
/** @var common\models\LoanOptionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Loan Options');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-option-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Loan Option'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'association',
            'name',
            [
                'attribute'=>'bank_account', 
                'vAlign'=>'middle',
                //'width'=>'20%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return BankAccount::findOne($model->bank_account)->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                //'filter' => ArrayHelper::map(BankAccount::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => BankAccount::getAllBankAccountsOfCurrentAssociation(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Bank Accounts')],
                'format'=>'raw'
            ],
            'min_amount',
            'max_amount',
            'interest_rate',
            'number_of_terms',
            [
                'attribute' => 'term_duration',
                'value'=>function ($model, $key, $index, $widget) { 
                    return LoanOption::getListOfAllTermDurations()[$model->term_duration];
                },
            ],
            //'postpone_option',
            //'postpone_capital',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {terms} {update} {delete}',
                'urlCreator' => function ($action, LoanOption $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

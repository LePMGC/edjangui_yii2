<?php

use common\models\CashOut;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\User;
use common\models\BankAccount;
/** @var yii\web\View $this */
/** @var common\models\CashOutSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Cash Outs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-out-index">

    <p>
        
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'member', 
                'vAlign'=>'middle',
                'width'=>'25%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return User::findOne($model->member)->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                //'filter'=>ArrayHelper::map(User::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => User::getAllUsers(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Members')],
                'format'=>'raw'
            ],
             [
                'attribute'=>'bank_account', 
                'vAlign'=>'middle',
                'width'=>'25%',
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
            'amount',
            'balance_before',
            'balance_after',
            //'association',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CashOut $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

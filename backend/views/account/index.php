<?php

use common\models\Account;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\User;
use common\models\BankAccount;

/** @var yii\web\View $this */
/** @var common\models\AccountSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Account'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute'=>'owner', 
                'vAlign'=>'middle',
                //'width'=>'20%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return User::findOne($model->owner)->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                //'filter'=>ArrayHelper::map(User::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => User::getAllMembersOfCurrentAssociation(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Users')],
                'format'=>'raw'
            ],

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
            [
                'attribute' => 'balance',
                'pageSummary' => true,
                'format' => 'Decimal',
            ],
            //'association',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'template' => '{view}',
                'urlCreator' => function ($action, Account $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
        'showPageSummary' => true,
        'pageSummaryRowOptions' => [
            'class' => 'kv-page-summary primary',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

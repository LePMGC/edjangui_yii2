<?php

use common\models\BankAccount;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\BaseUrl;
/** @var yii\web\View $this */
/** @var common\models\BankAccountSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Bank Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-account-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Bank Account'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'loan_allowed',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->loan_allowed ? Yii::t('app', 'YES') : Yii::t('app', 'NO');
                },
            ],
            [
                'attribute' => 'cash_out_allowed',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->cash_out_allowed ? Yii::t('app', 'YES') : Yii::t('app', 'NO');
                },
            ],
            'fix_cash_in_amount',
            'min_balance_for_loan',
            'min_cash_in_amount',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, BankAccount $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

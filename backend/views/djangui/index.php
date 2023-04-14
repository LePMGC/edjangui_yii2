<?php

use common\models\Djangui;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\BankAccount;
/** @var yii\web\View $this */
/** @var common\models\DjanguiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Djanguis');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Djangui'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'amount',
            'penalty_type',
            [
                'attribute' => 'penalty_type',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->penalty_type == 0 ? Yii::t('app', 'Fix Amount') : Yii::t('app', 'Percentage');
                },
            ],
            'penalty_amount',
            [
                'attribute'=>'penalty_account', 
                'vAlign'=>'middle',
                //'width'=>'20%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return BankAccount::findOne($model->penalty_account)->name;
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
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Djangui $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

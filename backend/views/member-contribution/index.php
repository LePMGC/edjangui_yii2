<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Episode;
use common\models\Season;
use common\models\User;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel common\models\DjanguiContributionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Djangui Contributions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-contribution-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Member Contribution'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'episode', 
                'vAlign'=>'middle',
                'width'=>'25%',
                /*'value'=>function ($model, $key, $index, $widget) { 
                    return Episode::findOne($model['episode'])->name;
                },*/
                'filterType' => GridView::FILTER_SELECT2,
                //'filter' => ArrayHelper::map(BankAccount::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => Episode::getAllEpisodes(Season::getCurrentSeasonId()),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Episodes')],
                'format'=>'raw'
            ],

            [
                'attribute'=>'member', 
                'vAlign'=>'middle',
                'width'=>'25%',
                /*'value'=>function ($model, $key, $index, $widget) { 
                    return User::findOne($model['member'])->name;
                },*/
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
                'attribute' => 'djangui_contributions',
                'format' => 'raw'
            ],
            [
                'attribute' => 'bank_contributions',
                'format' => 'raw'
            ],

            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

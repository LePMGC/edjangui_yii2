<?php

use common\models\DjanguiContribution;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\User;
use common\models\Djangui;
use common\models\Episode;
use common\models\Season;
/** @var yii\web\View $this */
/** @var common\models\DjanguiContributionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Djangui Contributions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-contribution-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Djangui Contribution'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'filter' => User::getAllMembersOfCurrentAssociation(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Users')],
                'format'=>'raw'
            ],
            [
                'attribute'=>'episode', 
                'vAlign'=>'middle',
                'width'=>'25%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return Episode::findOne($model->episode)->name;
                },
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
                'attribute'=>'djangui', 
                'vAlign'=>'middle',
                'width'=>'25%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return Djangui::findOne($model->djangui)->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                //'filter' => ArrayHelper::map(BankAccount::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => Djangui::getAllDjanguis(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Djanguis')],
                'format'=>'raw'
            ],
            //'expected_amount',
            'contribution_amount',
            //'association',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DjanguiContribution $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

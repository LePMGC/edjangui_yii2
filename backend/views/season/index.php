<?php

use common\models\Season;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\SeasonSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Seasons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Season'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'start_date',
            'end_date',
            'periodicity',
            //'current_episode',
            'meeting_day',
            //'association',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Season $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

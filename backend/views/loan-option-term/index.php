<?php

use common\models\LoanOptionTerm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\LoanOptionTermSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Loan Option Terms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-option-term-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Loan Option Term'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'association',
            'name',
            'amount_to_refurn',
            'percentage',
            //'loan_option',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LoanOptionTerm $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

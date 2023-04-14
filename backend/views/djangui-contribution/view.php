<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\Djangui;
use common\models\Episode;

/** @var yii\web\View $this */
/** @var common\models\DjanguiContribution $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Djangui Contributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="djangui-contribution-view">

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [ 
                        'label' => $model->getAttributeLabel('member'),
                        'value' => User::findOne($model->member)->name,
                    ],
            [ 
                        'label' => $model->getAttributeLabel('episode'),
                        'value' => Episode::findOne($model->episode)->name,
                    ],
            [ 
                        'label' => $model->getAttributeLabel('djangui'),
                        'value' => Djangui::findOne($model->djangui)->name,
                    ],
            'expected_amount',
            'contribution_amount',
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

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\BankAccount;
use common\models\Episode;


/** @var yii\web\View $this */
/** @var common\models\CashIn $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Ins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cash-in-view">

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
                        'label' => $model->getAttributeLabel('bank_account'),
                        'value' => BankAccount::findOne($model->bank_account)->name,
                    ],
            'amount',
            'balance_before',
            'balance_after',
            //'association',
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

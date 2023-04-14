<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\BankAccount;

/** @var yii\web\View $this */
/** @var common\models\Account $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="account-view">

    <p>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [ 
                        'label' => $model->getAttributeLabel('owner'),
                        'value' => User::findOne($model->owner)->name,
                    ],
            [ 
                        'label' => $model->getAttributeLabel('bank_account'),
                        'value' => BankAccount::findOne($model->bank_account)->name,
                    ],
            'balance',
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

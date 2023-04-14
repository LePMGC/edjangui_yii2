<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\LoanOption;
use common\models\Episode;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var common\models\Season $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="season-view">

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

    <div class="row">
        <div class="col-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'start_date',
                    'end_date',
                    'periodicity',
                    'current_episode',
                    [ 
                                'label' => $model->getAttributeLabel('meeting_day'),
                                'value' => LoanOption::getNameOfRedundDeadline($model->meeting_day),
                            ],
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
                ]

            ]) ?>
        </div>
        <div class="col-7">
            <center> <strong> <?= Yii::t('app', 'Episodes') ?> </strong> </center>
             <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            
            <?php 
                if($currentAction == "VIEW"){                        
                    echo Html::a(Yii::t('app', '+Episode'), ['season/view', 'id' => $model->id, 'currentAction' => 'ADD'], ['class' => 'btn btn-primary']);
                }else{                          
                    echo Html::a(Yii::t('app', 'Episodes'), ['season/view', 'id' => $model->id, 'currentAction' => 'VIEW'], ['class' => 'btn btn-primary']);
                }
            ?>
            <br/>
            <br/>
            <?php

                if(($currentAction == "UPDATE") || ($currentAction == "ADD")){
                    $form = ActiveForm::begin();

                        echo $form->field($modelEpisode, 'name')->textInput();

                        echo $form->field($modelEpisode, 'rank')->textInput();

                        echo $form->field($modelEpisode, 'start_date')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Enter Start date ...'],
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);

                        echo $form->field($modelEpisode, 'end_date')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Enter End date ...'],
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);

                        echo $form->field($modelEpisode, 'meeting_date')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Enter End date ...'],
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);
                        
                        echo '<div class="form-group">';
                        echo Html::submitButton($modelEpisode->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $modelEpisode->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                        echo '</div>';
                    ActiveForm::end();

                }else{

                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        //'filterModel' => $searchModel,
                        'layout' => '{items}{pager}',
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],
                            'rank',
                            'name',
                            'start_date',
                            'end_date',
                            'meeting_date',
                            //'loan_option',
                            //'created_by',
                            //'created_on',
                            //'updated_by',
                            //'updated_on',
                            [
                                'class' => ActionColumn::className(),
                                'template' => '{update}{delete}',
                                'urlCreator' => function ($action, Episode $model, $key, $index, $column) {
                                    return Url::toRoute(['view', 'id' => $model->season,'currentAction' =>strtoupper($action) , 'episodeId' => $model->id]);
                                 }
                            ],
                        ],
                    ]); 
                }
            ?>

    <?php Pjax::end(); ?>
        </div>
    </div>
</div>

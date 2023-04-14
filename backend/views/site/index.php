<?php

/** @var yii\web\View $this */

use common\models\Loan;
use common\models\Djangui;
use common\models\User;
use common\models\BankAccount;

$this->title = Yii::$app->name;
$modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

?>
<div class="site-index">
    <div class="body-content">
    	<div class="row">
			<div class="col-lg-3">
		        <div class="card card-success">
		                <div class="card-body">
		                	<center> <strong> <font size="15px"> <?= count(User::find()->where(['association'=>$modelCurrentUser->association])->all()) -1 ?> </font> </strong> </center>
		                </div>
		            <div class="card-footer"> 
		            	<center> <?= Yii::t('app', 'Members') ?> </center>
		            </div>                        
		        </div>
		    </div>

		    <div class="col-lg-3">
		        <div class="card card-danger">
		                <div class="card-body">
		                	<center> <strong> <font size="15px"> <?= count(Djangui::find()->where(['association'=>$modelCurrentUser->association])->all())?> </font> </strong> </center>
		                </div>
		            <div class="card-footer"> 
		            	<center> <?= Yii::t('app', 'Djanguis') ?> </center>
		            </div>
		        </div>
		    </div>

		    <div class="col-lg-3">
		        <div class="card card-primary">
		                <div class="card-body">
		                	<center> <strong>  <font size="15px"> <?= count(BankAccount::find()->where(['association'=>$modelCurrentUser->association])->all())?> </font> </strong> </center>
		                </div>
		            <div class="card-footer"> 
		            	<center> <?= Yii::t('app', 'Bank Accounts') ?> </center>
		            </div>
		        </div>
		    </div>

		    <div class="col-lg-3">
		        <div class="card card-default">
		                <div class="card-body">
		                	<center> <strong>  <font size="15px"> <?= count(Loan::find()->where(['association'=>$modelCurrentUser->association])->andWhere('status <> '.Loan::LOAN_RETURNED.' and status <> '.Loan::LOAN_REJECTED)->all())?> </font> </strong> </center>
		                </div>
		            <div class="card-footer"> 
		            	<center> <?= Yii::t('app', 'Open Loans') ?> </center>
		            </div>
		        </div>
		    </div>
		</div>

		<hr>

		<div class="row">
			<div class="col-lg-3">
		        <div class="card border-info">
		            <div class="card-body">
		            	<table class="table table-striped" style="font-size:13px">
		            	<?php
		            		$modelMembers = User::find()->where(['association'=>$modelCurrentUser->association])
		            									->andWhere('id not in (select admin_user_id from association)')
		            									->orderBy('name')->all();
		            		foreach ($modelMembers as $modelMember) {
		            			echo "<tr>";
		            				echo "<td>".$modelMember->name."</td>";
		            			echo "</tr>";
		            		}
		            	?>
		            	</table>
		            </div>
		        </div>
		    </div>

		    <div class="col-lg-3">
		        <div class="card border-success">
		            <div class="card-body">
		            	<table class="table table-striped" style="font-size:13px">
		            	<?php
		            		$modelDjanguis = Djangui::find()->where(['association'=>$modelCurrentUser->association])->orderBy('name')->all();
		            		foreach ($modelDjanguis as $modelDjangui) {
		            			echo "<tr>";
		            				echo "<td>".$modelDjangui->name."</td>";
		            			echo "</tr>";
		            		}
		            	?>
		            	</table>
		            </div>
		        </div>
		    </div>

			<div class="col-lg-3">
		        <div class="card border-warning">
		            <div class="card-body">
		            	<table class="table table-striped" style="font-size:13px">
		            	<?php
		            		$modelBankAccounts = BankAccount::find()->where(['association'=>$modelCurrentUser->association])->orderBy('name')->all();
		            		foreach ($modelBankAccounts as $modelBankAccount) {
		            			echo "<tr>";
		            				echo "<td>".$modelBankAccount->name."</td>";
		            			echo "</tr>";
		            		}
		            	?>
		            	</table>
		            </div>
		        </div>
		    </div>

		    <div class="col-lg-3">
		        <div class="card border-danger">
		            <div class="card-body">
		            	<table class="table table-striped" style="font-size:13px">
		            	<?php
		            		$modelLoans = Loan::find()->where(['association'=>$modelCurrentUser->association])->andWhere('status <> '.Loan::LOAN_RETURNED.' and status <> '.Loan::LOAN_REJECTED)->all();
		            		$totalAmountToRefund = 0;
		            		foreach ($modelLoans as $modelLoan) {
		            			$modelTaker = User::findOne($modelLoan->taker);
		            			$amountToRefund = $modelLoan->getAmountToDisplayInFrontend();
		            			$totalAmountToRefund += $amountToRefund;
		            			echo "<tr>";
		            				echo "<td>".$modelTaker->getFirstName()."</td>";
		            				echo "<td>".\Yii::$app->formatter->asDecimal($amountToRefund)."</td>";
		            			echo "</tr>";
		            		}
		            	?>
		            		<tfoot>
		            			<tr>
		            				<td> <strong> <?= \Yii::t('app', 'Total') ?> </strong> </td>
		            				<td> <strong> <?= \Yii::$app->formatter->asDecimal($totalAmountToRefund) ?> </strong> </td>
		            			</tr>
		            		</tfoot>
		            	</table>
		            </div>
		        </div>
		    </div>

		</div>
    </div> 
</div>

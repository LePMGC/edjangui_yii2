<?php
use common\models\LoanEndorse;
use common\models\Episode;
use common\models\Loan;
use common\models\User;
use frontend\models\Member;
use common\models\Djangui;
use yii\helpers\Url;
use yii\helpers\BaseHtml;
use kartik\bs4dropdown\ButtonDropdown;
use yii\helpers\Html;
use common\models\BankAccount;
use app\models\EpisodeContributionSearch;
use xstreamka\mobiledetect\Device;
/** @var yii\web\View $this */

$this->title = 'eDjangui';
    $currentUser = User::findOne(Yii::$app->user->getId());
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card border-primary">
                <div class="card-header"> <center> <?= Yii::t('app', 'Profile') ?> </center>  </div>
                <div class="card-body">
                    <table class="table table-striped" style="font-size:13px">
                            <?php
                                $accountNameAndBalances = Member::getAllAccountsOfMember($currentUser->id);
                                foreach ($accountNameAndBalances as $accountNameAndBalance) {
                                    echo "<tr>";
                                        echo "<td>";
                                            echo Yii::t('app', 'Balance')." <strong> &nbsp".$accountNameAndBalance['name']."</strong>";
                                        echo "</td>";

                                        echo "<td>";
                                            echo Yii::$app->formatter->asDecimal($accountNameAndBalance['balance']);
                                        echo "</td>";
                                    echo "</tr>";   
                                }

                                echo "<tr> <td> </td> <td> </td> </tr>";

                                $djanguiNameAndEpisodes = Member::getAllDjanguisOfMember($currentUser->id);
                                foreach ($djanguiNameAndEpisodes as $djanguiNameAndEpisode) {
                                    echo "<tr>";
                                        echo "<td>";
                                            echo Yii::t('app', '')." <strong> &nbsp".$djanguiNameAndEpisode['name']."</strong>";
                                        echo "</td>";

                                        echo "<td>";
                                            echo $djanguiNameAndEpisode['collecting_episode'];
                                        echo "</td>";
                                    echo "</tr>";   
                                }

                                echo "<tr> <td> </td> <td> </td> </tr>";
                                //Total Cash In
                                $totalCashInOfTheMember = Member::getTotalCashInAmounts($currentUser->id);
                                echo "<tr>";
                                    echo "<td>";
                                        echo "<strong>".Yii::t('app', 'Total Cash In')."</strong>";
                                    echo "</td>";

                                    echo "<td>";
                                        echo Yii::$app->formatter->asDecimal($totalCashInOfTheMember['current_season'])." / ".Yii::$app->formatter->asDecimal($totalCashInOfTheMember['all_the_time']);
                                    echo "</td>";
                                echo "</tr>";

                                //Total Cash Out
                                $totalCashOutOfTheMember = Member::getTotalCashOutAmounts($currentUser->id);
                                echo "<tr>";
                                    echo "<td>";
                                        echo "<strong>".Yii::t('app', 'Total Cash Out')."</strong>";
                                    echo "</td>";

                                    echo "<td>";
                                        echo Yii::$app->formatter->asDecimal($totalCashOutOfTheMember['current_season'])." / ".Yii::$app->formatter->asDecimal($totalCashOutOfTheMember['all_the_time']);
                                    echo "</td>";
                                echo "</tr>";

                                //Loan Interest Shares
                                $totalInterestShareOfTheMember = Member::getTotalInterestShares($currentUser->id);
                                echo "<tr>";
                                    echo "<td>";
                                        echo "<strong>".Yii::t('app', 'Total Interest Share')."</strong>";
                                    echo "</td>";

                                    echo "<td>";
                                        echo Yii::$app->formatter->asDecimal($totalInterestShareOfTheMember['current_season'])." / ".Yii::$app->formatter->asDecimal($totalInterestShareOfTheMember['all_the_time']);
                                    echo "</td>";
                                echo "</tr>";
                            ?>
                    </table>
                </div>
                <div class="card-footer">
                    
                        <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/edit-profile']).'"' ?> >
                                <?= Yii::t('app', 'Edit Profile')?>
                            </a>
                        <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/loan-endorse-requests']).'"' ?> >
                            <?php
                                echo Yii::t('app', 'Endorse Requests');
                                $numberOfNewEndorseRequests = LoanEndorse::getNumberOfNewEndorseRequests($currentUser->id);
                                if($numberOfNewEndorseRequests > 0 )
                                    echo '&nbsp<span class="badge badge-pill badge-success">'.$numberOfNewEndorseRequests.'</span>';
                            ?>                            
                        </a>
                    
                </div>
            </div>
        </div>

        <?php if (Device::$isPhone) { echo "<p> </p>";} ?>

        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card border-info">
                <div class="card-header"> <center> <?= Yii::t('app', 'Account History') ?> </center>  </div>
                <div class="card-body">
                    <?php
                            $accountHistoryData = $currentUser->getAccountHistoryData(6);                            
                            if(count($accountHistoryData)==0){
                                echo Yii::t('app', "Your Account History is empty");
                            }else{
                                echo '<table class="table table-striped" style="font-size:13px">';
                                    echo '<tr>';
                                        echo '<th>'.Yii::t('app', 'Date').'</th>';
                                        echo '<th>'.Yii::t('app', 'Type').'</th>';
                                        echo '<th>'.Yii::t('app', 'Amount').'</th>';
                                        echo '<th>'.Yii::t('app', 'Balance After').'</th>';
                                    echo '</tr>';
                                    foreach ($accountHistoryData as $accountHistoryDataItem) {
                                        //Build date format, display year only if it is different than current year.
                                        if(strcmp(date('Y', strtotime($accountHistoryDataItem['ah_date'])), Date('Y')) == 0)
                                            $dateFormatTemplate = "php:  M d";
                                        else
                                            $dateFormatTemplate = "php:  Y M d";
                                        echo '<tr>';
                                            echo '<td>'.Yii::$app->formatter->asDateTime($accountHistoryDataItem['ah_date'], $dateFormatTemplate).'</td>';
                                            echo '<td>'.$accountHistoryDataItem['ah_type'].'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($accountHistoryDataItem['ah_amount'],0).'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($accountHistoryDataItem['ah_balance_after'],0).'</td>';
                                        echo '</tr>';
                                    }
                                echo '</table>';
                            }
                        ?>
                </div>
                <div class="card-footer">
                    
                        <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/account-history-view-more']).'"' ?> >
                            <?= Yii::t('app', 'More').' ...'?>                                 
                        </a>
                                 
                        <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/account-history-detailled']).'"' ?> >
                             <?= Yii::t('app', 'Detailled')?>
                        </a>

                        <?php
                            $modelCurrentBankAccount = BankAccount::findOne($_SESSION['account_history_bank_account']);
                            $modelBankAccounts = BankAccount::find()->where(['association' => $currentUser->association])->all();
                            $i = 0;
                            $buttonDropdownItems = array();
                            foreach ($modelBankAccounts as $modelBankAccount) {
                               $buttonDropdownItems[$i++] = array(
                                    'label' => $modelBankAccount->name,
                                    'url' => Url::to(['member/set-account-history-bank-account', 'bankAccount' => $modelBankAccount->id])
                               );
                            }

                            echo ButtonDropdown::widget([
                                'label' => ''.(is_null($modelCurrentBankAccount) ? '' : $modelCurrentBankAccount->name), 
                                'dropdown' => [
                                    'items' => $buttonDropdownItems
                                ],
                                'buttonOptions' => ['class' => 'btn-outline-secondary']
                            ]);
                        ?>

                        <!-- <a class="btn btn-success" href=<?= '"'.Url::to(['/member/cash-out']).'"' ?>><?= Yii::t('app', 'Cash Out')?></a> -->
                    
                </div>
            </div>
        </div>

        <?php if (Device::$isPhone) { echo "<p> </p>";} ?>

        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card border-primary">
                <div class="card-header"> 
                    <center> 
                        <?php $totalAmountAvailableForLoan = Loan::getTotalAmountAvailableForLoan(); ?>
                        <?= Yii::t('app', 'Loans') ?> 
                        <h7> (<?= '<strong>'.Yii::$app->formatter->asCurrency($totalAmountAvailableForLoan).'</strong>' ?> <?= Yii::t('app', 'avaialble')?> ) </h7> 
                    </center>  
                </div>
                <div class="card-body">
                    <?php
                            $modelLoans = Loan::find()->where(['association' => $currentUser->association])->orderBy('created_on DESC')->limit(6)->all();
                            if(count($modelLoans)==0){
                                echo "There are no loans";
                            }else{
                                echo '<table class="table table-striped" style="font-size:13px">';
                                    echo '<tr>';
                                        echo '<th>'.Yii::t('app', 'Date').'</th>';
                                        echo '<th>'.Yii::t('app', 'Taker').'</th>';
                                        echo '<th>'.Yii::t('app', 'Amount').'</th>';
                                        echo '<th>'.Yii::t('app', 'Status').'</th>';
                                    echo '</tr>';
                                    foreach ($modelLoans as $modelLoan) {
                                        $loanTakerFirstName = User::findOne($modelLoan->taker)->name;
                                        $arr = explode(' ',trim($loanTakerFirstName));
                                        $loanTakerFirstName = $arr[0];

                                        if(strcmp(date('Y', strtotime($modelLoan->taken_date)), Date('Y')) == 0)
                                            $dateFormatTemplate = "php:  M d";
                                        else
                                            $dateFormatTemplate = "php:  Y M d";

                                        echo '<tr>';
                                            echo '<td>'.Yii::$app->formatter->asDateTime($modelLoan->taken_date, $dateFormatTemplate).'</td>';
                                            echo '<td>'.$loanTakerFirstName.'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($modelLoan->getAmountToDisplayInFrontend(),0).'</td>';
                                            echo '<td>'.$modelLoan->getStatusForFrontend().'</td>';
                                        echo '</tr>';
                                    }                                
                                echo '</table>';
                            }
                        ?> 
                </div>
                <div class="card-footer">
                    
                    <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/loans-view-more']).'"' ?> <?= (count($modelLoans)>0) ? '' : 'disabled' ?> >
                        <?= Yii::t('app', 'More').' ...'?>                                 
                    </a>
                    <a class="btn btn-primary" href=<?= '"'.Url::to(['/member/loans-detailled']).'"' ?> <?= (count($modelLoans)>0) ? '' : 'disabled' ?> >
                        <?= Yii::t('app', 'Detailled')?>                                 
                    </a>
                    <a>
                        <?php
                            if ($totalAmountAvailableForLoan > 100) echo BaseHtml::a(Yii::t('app', 'Ask a Loan'), Url::to(['/member/ask-a-loan']), ['class' => "btn btn-success"]);
                        ?>

                    </a>
                        
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <p> </p>
        <hr>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card border-primary">
                <div class="card-header"> 
                    <table class="table">
                        <tr>
                            <td> 
                                <strong> <?= Yii::t('app', 'Current Episode').' : ' ?> </strong>
                                <?= Episode::getCurrentEpisodeName() ?>
                            </td>

                            <td> 
                                <strong> <?= Yii::t('app', 'Djangui').' : ' ?> </strong>
                                
                                <?php
                                    $modelDjanguis = Djangui::find()->where(['association' => $currentUser->association])->all();
                                    $i = 0;
                                    $buttonDropdownDjanguiItems = array();
                                    foreach ($modelDjanguis as $modelDjangui) {
                                       $buttonDropdownDjanguiItems[$i++] = array(
                                            'label' => $modelDjangui->name,
                                            'url' => Url::to(['member/set-members-contributions-current-djangui', 'djangui' => $modelDjangui->id])
                                       );
                                    }
                                    if(!isset($_SESSION['members_contributions_current_djangui'])){
                                        $session = Yii::$app->session;
                                        $session->open();
                                        $session->set('members_contributions_current_djangui', Djangui::getFirstCreatedDjanguiId());
                                    }
                                    $modelCurrentDjangui = Djangui::findOne($_SESSION['members_contributions_current_djangui']);
                                ?>
                                <?= ButtonDropdown::widget([
                                    'label' => ''.(is_null($modelCurrentDjangui) ? '' : $modelCurrentDjangui->name), 
                                    'dropdown' => [
                                        'items' => $buttonDropdownDjanguiItems
                                    ],
                                    'buttonOptions' => ['class' => 'btn-outline-secondary']
                                ]); ?>
                            </td>
                        </tr>
                    </table> 
                </div>
                <div class="card-body">
                    <?php
                        //$accountNameAndBalances = Member::getAllAccountsOfMember($currentUser->id);
                        $episodeContributionData = EpisodeContributionSearch::getCurrentEpisodeContributionsData($_SESSION['members_contributions_current_djangui']); 
                    ?>
                    <table class="table table-sm table-striped" style="font-size:13px">
                            <tr>
                                <th> <?= Yii::t('app', 'Member') ?> </th>
                                <th class="hidden-xs hidden-sm"> <?= Yii::t('app', 'Collecting Episode')?> </th>
                                <th> <?= Yii::t('app', 'Djangui')?> </th>
                            </tr>
                            <?php
                                $sumDjanguiContributions = 0;
                                $sumBankContributions = 0;
                                $sumTransactionAmounts = 0;
                                foreach ($episodeContributionData as $episodeContributionDataItem) {
                                    echo "<tr ".($episodeContributionDataItem['current_episode_id']==$episodeContributionDataItem['collecting_episode_id'] ? "class='table-primary'" : '').">";
                                        echo "<td>".$episodeContributionDataItem['member']."</td>";
                                        echo "<td class='hidden-xs hidden-sm'>".$episodeContributionDataItem['collecting_episode']."</td>";
                                        echo "<td>";
                                            echo $episodeContributionDataItem['djangui_contribution'] > 0 ? Yii::$app->formatter->asDecimal($episodeContributionDataItem['djangui_contribution'],0) : '';
                                        echo "</td>";
                                    echo "</tr>";
                                    $sumDjanguiContributions += $episodeContributionDataItem['djangui_contribution'];
                                }
                            ?>
                            <tfoot>
                                <tr>                                    
                                    <th> <?= Yii::t('app', 'Total')?> </th>
                                    <th class="hidden-xs hidden-sm"> </th>
                                    <th> <?= $sumDjanguiContributions>0 ? Yii::$app->formatter->asDecimal($sumDjanguiContributions,0) : '' ?> </th>
                                </tr>
                            </tfoot>
                        </table>                    
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
        </div>
    </div>
</div>

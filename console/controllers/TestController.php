<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\CashOut;
use common\models\CashIn;
use common\models\Loan;
use common\models\LoanOption;
use common\models\LoanEndorse;
use common\models\LoanRefund;
use common\models\User;
use common\models\EmailNotification;
use common\models\Parameter;
use common\models\SmsNotification;
use common\models\Notification;
use common\models\Season;
use common\models\DjanguiMember;
use common\models\Djangui;
use common\models\Episode;
use common\models\Account;
use common\models\BankAccount;
use common\models\DjanguiContribution;
use common\models\Association;
use common\models\InterestShare;
use yii\httpclient\Client;
use Yii;

/**
 * Test controller
 */
class TestController extends Controller {

    public function actionIndex() {
        echo "cron service runnning";
    }

    public function actionMail($to) {
        echo "Sending mail to " . $to;
    }

    /**
    * Browse all unsent emails, procceed with email sending and update the status
    */
    public function actionSendEmailNotification(){
        $modelEmailNotifications = EmailNotification::find()->where(['sending_status' => 0])->all();
        //echo count($modelEmailNotifications);
        foreach ($modelEmailNotifications as $modelEmailNotification) {

            if(Yii::$app->mailer
                ->compose(
                    ['html' => 'emailNotification-html', 'text' => 'emailNotification-text'],
                    ['emailNotificationHtmlContent' => $modelEmailNotification->html_content, 'emailNotificationTextContent' => $modelEmailNotification->text_content]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                ->setTo($modelEmailNotification->send_to)
                ->setSubject($modelEmailNotification->subject)
                ->send()){

                $modelEmailNotification->sending_status = 1;
                $modelEmailNotification->sent_on = Date("Y-m-d H:i:s");
                $modelEmailNotification->save();

                echo Date("Y-m-d H:i:s")." Email sent to ".$modelEmailNotification->send_to." SUCCESS";
            }else
                echo Date("Y-m-d H:i:s")." Email sent to ".$modelEmailNotification->send_to." FAILED";
            echo "<br/>";

        }
    }


    /**
    * Browse all unsent SMSs, procceed with SMS sending and update the status
    */
     /*
    * Send SMS notifications
    */
    public function actionSendSmsNotification(){
        $modelSmsNotifications = SmsNotification::find()
            ->where(['status' => 0])
            //->andWhere(['not like','description', 'Invalid Destination'])
            ->limit(1)
            ->all();

        $client = new Client();

        foreach ($modelSmsNotifications as $modelSmsNotification) {

            $url = "https://obitsms.com/api/v2/bulksms?key_api=8Vc3fKgJlHrS19tJ2TrZ6XiY8Yb5DFad&sender=eDjangui&destination=237".$modelSmsNotification->phone_number."&message=".urlencode($modelSmsNotification->content);
            
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                //->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
                //->setOptions([
                    //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                    //'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                //])
                ->send();

            $modelSmsNotification->status = ($response->getData()['success']) ? 1 : 0;
            $modelSmsNotification->description = $response->getData()['success']."/".$response->getData()['message'];
            echo Date("Y-m-d H:i:s")."\n".$modelSmsNotification->phone_number." - ";
            print_r($response->getData());
            echo "\n\n";

            $modelSmsNotification->save();
            
        }
    }

    public function actionSendBackupToAdmin(){
        $backupFiles = scandir("/var/tmp/dbbackup/");
        if(count($backupFiles)>1){
            $edjanguiBackupFileFound = false;
            $pahbehGazBackupFileFound = false;
            $numberOfFiles = count($backupFiles);
            $i = 0;
            $edjanguiBackupFileBeginWith = "edjangui_backup_".date('dmY',strtotime("-1 days"));
            while (($i < $numberOfFiles) && !($edjanguiBackupFileFound && $pahbehGazBackupFileFound)) {

                if(strstr($backupFiles[$i], $edjanguiBackupFileBeginWith)){
                    $edjanguiBackupFileFound = true;
                    $edjanguiBackupFile = "/var/tmp/dbbackup/".$backupFiles[$i];
                }
                $i++;
            }

            //$edjanguiBackupFile = "/var/tmp/dbbackup/".$edjanguiBackupFileFound;
            //$pahbehGazBackupFile = "/var/tmp/dbbackup/".$pahbehGazBackupFileFound;

            /*echo $edjanguiBackupFile."\n";
            echo $pahbehGazBackupFile."\n";
            echo $numberOfFiles."\n";
            echo $i."\n";
            echo $edjanguiBackupFile."\n";
            echo $pahbehGazBackupFile."\n";*/
        }

        Yii::$app->mailer
                ->compose(
                    [
                        'html' => 'emailNotification-html',
                        'text' => 'emailNotification-text'
                    ],
                    [
                        'emailNotificationHtmlContent' => "Edjangui - Backup", 
                        'emailNotificationTextContent' => "<p> Edjangui - Backup </p>"
                    ]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                ->setTo("dore.pmgc@gmail.com")
                ->setSubject("Edjangui - Backup - ".Date("Y-m-d H:i:s"))
                ->attach($edjanguiBackupFile)
                ->attach($pahbehGazBackupFile)
                ->send();
    }

    public function actionPrepareEmailNotificationForUserAccounts(){
        $modelUsers = User::find()->where('id in (15)')->all();
        foreach ($modelUsers as $modelUser) {
            //generate new password
            $currenUserPassword = User::generateStrongPassword();
            $modelUser->setPassword($currenUserPassword);
            $modelUser->generateAuthKey();
            $modelUser->save();

            $modelEmailNotification = new EmailNotification();
            $modelEmailNotification->subject = 'e-Djangui Support : Your Account Details';
            $modelEmailNotification->html_content = '<p> Hello '.$modelUser->name.',</p> 
                <p> An user account have been created for you on e-Djangui Online.</p>
                <p> See below the access details : </p>
                <p>     - Url : http://www.edjangui.com/</p>
                <p>     - Username : '.$modelUser->username.'</p>
                <p>     - Password : '.$currenUserPassword.'</p>
                <br/>
                <p>Best Regards///</p>';

            $modelEmailNotification->text_content = 'Hello '.$modelUser->name.', 
                An user account have been created for you on e-Djangui Online.
                See below the access details :
                        - Url : http://www.edjangui.com/
                        - Username : '.$modelUser->username.'
                        - Password : '.$currenUserPassword.'

                Best Regards///';

            $modelEmailNotification->send_to = $modelUser->email_address;
            $modelEmailNotification->sending_status = 0;
            $modelEmailNotification->save();
        }
    }

    /**
    * Remind all users that we are on 3 days before djangui contributions day
    */
    public function actionSendDjanguiContributionReminderDaysBefore(){
        $modelNotification = Notification::find()->where(['name' => 'contribution_reminider_first_day'])->one();

    	//Get all associations
        $modelAssociations = Association::find()->where('id > 0')->all();
        $todaysDate = date('Y-m-d');
        foreach ($modelAssociations as $modelAssociation) {
            $modelSeason = Season::find()
                            ->where(['association' => $modelAssociation->id])
                            ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                            ->one();

            if(!is_null($modelSeason)){
                $modelEpisode = Episode::find()
                                ->where(['season' => $modelSeason->id])
                                ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                                ->one();

                if(!is_null($modelEpisode)){
                    $d = date_create($modelEpisode->meeting_date);
                    date_add($d, date_interval_create_from_date_string("23 days"));
                    if(strtotime($d->format('Y-m-d')) == strtotime($todaysDate)){
                        $modelDjanguiMembers = (new \yii\db\Query())
                                                ->select(['member', 'djangui', 'count(id) as number_of_names'])
                                                ->from('djangui_member')
                                                ->where(['association' => $modelAssociation->id, 'season' => $modelSeason->id])
                                                ->andWhere('collecting_episode <> '.$modelEpisode->id)
                                                ->groupBy('member', 'djangui')
                                                ->all();
                        foreach ($modelDjanguiMembers as $modelDjanguiMember) {
                            $modelMember = User::findOne($modelDjanguiMember['member']);
                            $modelDjangui = Djangui::findOne($modelDjanguiMember['djangui']);
                            $penaltyAmount = $modelDjangui->penalty_type ? $modelDjangui->penalty_amount : $modelDjangui->penalty_amount * $modelDjangui->amount / 100;
                            
                            //SMS Notification
                            $modelSmsNotification = new SmsNotification();
                            $modelSmsNotification->phone_number = $modelMember->phone_number;
                            //$modelSmsNotification->content = $modelNotification->sms_content;
                            if(strstr($modelMember->language, "fr")){
                                $modelSmsNotification->content = $modelNotification->sms_content_fr;
                                Yii::$app->formatter->locale = 'fr-FR';
                            }else{
                                $modelSmsNotification->content = $modelNotification->sms_content_en;
                                Yii::$app->formatter->locale = 'en-US';
                            }
                            $modelSmsNotification->content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[TODAY]", Date("Y-M-d"), $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_CONTRIBUTION_AMOUNT]", Yii::$app->formatter->asDecimal($modelDjangui->amount * $modelDjanguiMember['number_of_names']), $modelSmsNotification->content);            
                            $modelSmsNotification->content = str_replace("[DJANGUI_DELAY_CHARGES]", Yii::$app->formatter->asDecimal($penaltyAmount), $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelMember->name, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelSmsNotification->content);
                            $modelSmsNotification->status = 0;
                            $modelSmsNotification->save();

                            
                            //Email Notification
                            $modelEmailNotification = new EmailNotification();
                            $modelEmailNotification->send_to = $modelMember->email_address;
                            //$modelEmailNotification->html_content = $modelNotification->email_content;
                            if(strstr($modelMember->language, "fr")){
                                $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                                Yii::$app->formatter->locale = 'fr-FR';
                            }else{
                                $modelEmailNotification->html_content = $modelNotification->email_content_en;
                                Yii::$app->formatter->locale = 'en-US';
                            }
                            $modelEmailNotification->subject = 'eDjangui - Contribution Reminder';
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[TODAY]", Date("M d,Y h:m:s"), $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelEmailNotification->html_content); 
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_AMOUNT]", Yii::$app->formatter->asDecimal($modelDjangui->amount * $modelDjanguiMember['number_of_names']), $modelEmailNotification->html_content);             
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_DELAY_CHARGES]", Yii::$app->formatter->asDecimal($penaltyAmount), $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelMember->name, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['adminPhone'], $modelEmailNotification->html_content);
                            $modelEmailNotification->sending_status = 0;
                            $modelEmailNotification->save();
                            
                        }
                    }
                }
            }
        }
    }

    /**
    * Remind all users that we are on last day of djangui contributions
    */
    public function actionSendDjanguiContributionReminderLastDay(){
        $modelNotification = Notification::find()->where(['name' => 'contribution_reminider_last_day'])->one();

        //Get all associations
        $modelAssociations = Association::find()->where('id > 0')->all();
        $todaysDate = date('Y-m-d');

        foreach ($modelAssociations as $modelAssociation) {
            $modelSeason = Season::find()
                            ->where(['association' => $modelAssociation->id])
                            ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                            ->one();

            if(!is_null($modelSeason)){
                $modelEpisode = Episode::find()
                                ->where(['season' => $modelSeason->id])
                                ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                                ->one();

                if(!is_null($modelEpisode)){
                    if(strtotime($modelEpisode->meeting_date) == strtotime($todaysDate)){
                        $modelDjanguiMembers = (new \yii\db\Query())
                            ->select(['member', 'djangui', 'count(id) as number_of_names'])
                            ->from('djangui_member')
                            ->where(['association' => $modelAssociation->id, 'season' => $modelSeason->id])
                            ->andWhere('collecting_episode <> '.$modelEpisode->id)
                            ->andWhere('member not in (select member from djangui_member where collecting_episode = '.$modelEpisode->id.')')
                            ->andWhere('member not in (select member from djangui_contribution where episode = '.$modelEpisode->id.')')
                            ->groupBy('member', 'djangui')
                            ->all();
                        foreach ($modelDjanguiMembers as $modelDjanguiMember) {
                            $modelMember = User::findOne($modelDjanguiMember['member']);
                            $modelDjangui = Djangui::findOne($modelDjanguiMember['djangui']);
                            $penaltyAmount = $modelDjangui->penalty_type ? $modelDjangui->penalty_amount : $modelDjangui->penalty_amount * $modelDjangui->amount / 100;
                            
                            //SMS Notification
                            $modelSmsNotification = new SmsNotification();
                            $modelSmsNotification->phone_number = $modelMember->phone_number;
                            //$modelSmsNotification->content = $modelNotification->sms_content;
                            if(strstr($modelMember->language, "fr")){
                                $modelSmsNotification->content = $modelNotification->sms_content_fr;
                                Yii::$app->formatter->locale = 'fr-FR';
                            }else{
                                $modelSmsNotification->content = $modelNotification->sms_content_en;
                                Yii::$app->formatter->locale = 'en-US';
                            }
                            $modelSmsNotification->content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[TODAY]", Date("Y-M-d"), $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_CONTRIBUTION_AMOUNT]", Yii::$app->formatter->asDecimal($modelDjangui->amount * $modelDjanguiMember['number_of_names']), $modelSmsNotification->content);            
                            $modelSmsNotification->content = str_replace("[DJANGUI_DELAY_CHARGES]", Yii::$app->formatter->asDecimal($penaltyAmount), $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelMember->name, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelSmsNotification->content);
                            $modelSmsNotification->status = 0;
                            $modelSmsNotification->save();

                            
                            //Email Notification
                            $modelEmailNotification = new EmailNotification();
                            $modelEmailNotification->send_to = $modelMember->email_address;
                            //$modelEmailNotification->html_content = $modelNotification->email_content;
                            if(strstr($modelMember->language, "fr")){
                                $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                                Yii::$app->formatter->locale = 'fr-FR';
                            }else{
                                $modelEmailNotification->html_content = $modelNotification->email_content_en;
                                Yii::$app->formatter->locale = 'en-US';
                            }
                            $modelEmailNotification->subject = 'eDjangui - Contribution Reminder';
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[TODAY]", Date("M d,Y h:m:s"), $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelEmailNotification->html_content); 
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_AMOUNT]", Yii::$app->formatter->asDecimal($modelDjangui->amount * $modelDjanguiMember['number_of_names']), $modelEmailNotification->html_content);             
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_DELAY_CHARGES]", Yii::$app->formatter->asDecimal($penaltyAmount), $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelMember->name, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
                            $modelEmailNotification->sending_status = 0;
                            $modelEmailNotification->save();   
                        }
                    }
                }
            }
        }
    }

    /**
    * Check if we have already passed the deadline for djangui + bank contributions and charges those who have not yet contributed
    * Charges are debited on their bank balance and added to the admin bank balance.
    * Notification will be sent to all member who have been debited.
    */
    public function actionApplyContributionDelayCharges(){
        $modelAssociations = Association::find()->where('id > 0')->all();
        $todaysDate = date('Y-m-d');
        foreach ($modelAssociations as $modelAssociation) {
            $modelSeason = Season::find()
                            ->where(['association' => $modelAssociation->id])
                            ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                            ->one();

            if(!is_null($modelSeason)){
                $modelEpisode = Episode::find()
                                ->where(['season' => $modelSeason->id])
                                ->andWhere("start_date <='".$todaysDate."' and '".$todaysDate."' <= end_date")
                                ->one();

                if(!is_null($modelEpisode)){
                    if($modelEpisode->meeting_date < $todaysDate){
                        echo "\n\n Episode ".$modelEpisode->name;
                        $modelDjanguiMembers = (new \yii\db\Query())
                            ->select(['member', 'djangui', 'count(id) as number_of_names'])
                            ->from('djangui_member')
                            ->where(['association' => $modelAssociation->id, 'season' => $modelSeason->id])
                            ->andWhere('member not in (select member from djangui_member where collecting_episode = '.$modelEpisode->id.')')
                            ->andWhere('member not in (select member from djangui_contribution where episode = '.$modelEpisode->id.')')
                            ->groupBy('member', 'djangui')
                            ->all();
                        foreach ($modelDjanguiMembers as $modelDjanguiMember) {
                            $modelDjanguiContribution = DjanguiContribution::findOne(['member' => $modelDjanguiMember['member'], 'djangui' => $modelDjanguiMember['djangui'], 'episode' => $modelEpisode->id]);
                            if(is_null($modelDjanguiContribution) || (!is_null($modelDjanguiContribution) && $modelDjanguiContribution->contribution_amount != $modelDjanguiContribution->expected_amount)){

                                $modelMember = User::findOne($modelDjanguiMember['member']);
                                $modelDjangui = Djangui::findOne($modelDjanguiMember['djangui']);
                                echo "member = ".$modelMember->name.", djangui = ".$modelDjangui->name."\n";
                                
                                //create a loan for the member
                                $modelLoan = new Loan();
                                $modelLoan->taker = $modelMember->id;
                                $modelLoan->amount_requested = $modelDjangui->amount * $modelDjanguiMember['number_of_names'];
                                $modelLoan->amount_received = $modelLoan->amount_requested;
                                //$loanInterestsTakenAndReturnDate = Loan::getLoanInterestsTakenAndReturnDate($modelLoan->amount);
                                $modelLoan->taken_date = date('Y-m-d');
                                $modelLoan->return_date = date('Y-m-d');
                                $modelLoan->interest = ($modelDjangui->penalty_type == 1) ? $modelDjangui->penalty_amount : $modelDjangui->penalty_amount * $modelDjangui->amount / 100;
                                $modelLoan->status = Loan::LOAN_GIVEN;
                                $modelLoan->payment_method = Loan::LOAN_CASH_PAYMENT;
                                $modelLoan->phone_number = $modelMember->phone_number;
                                $modelLoanOption = LoanOption::find()->where('min_amount <= '.$modelLoan->amount_requested.' and '.$modelLoan->amount_requested.' <= max_amount')->one();
                                $modelLoan->association = $modelAssociation->id;
                                $modelLoan->bank_account = $modelDjangui->penalty_account;
                                $modelLoan->save();

                                //assign admin as loan endorser
                                $modelLoanEndorse = new LoanEndorse();
                                $modelLoanEndorse->loan = $modelLoan->id;
                                $modelLoanEndorse->endorser = $modelAssociation->admin_user_id;
                                $modelLoanEndorse->status = LoanEndorse::LOAN_ENDORSE_APPROVED;
                                $modelLoanEndorse->association = $modelAssociation->id;
                                $modelLoanEndorse->save();

                                //create interest share for all the other members
                                $modelLoan->initializeInterestsAndShare();

                                //create and save the djangui_contribution records                                
                                $modelDjanguiContribution = new DjanguiContribution();
                                $modelDjanguiContribution->episode = $modelEpisode->id;
                                $modelDjanguiContribution->member = $modelMember->id;
                                $modelDjanguiContribution->contribution_amount = $modelDjangui->amount * $modelDjanguiMember['number_of_names'];
                                $modelDjanguiContribution->expected_amount = $modelDjangui->amount * $modelDjanguiMember['number_of_names'];
                                $modelDjanguiContribution->association = $modelAssociation->id;
                                $modelDjanguiContribution->save();


                                //perform cash out operation on member account
                                $modelAccount = Account::find()->where(['owner' => $modelMember->id])
                                                ->andWhere('bank_account in (select id from bank_account where loan_allowed = 1)')
                                                ->one();
                                if(is_null($modelAccount)){
                                    $modelAccount = new Account();
                                    $modelAccount->bank_account = $modelLoan->bank_account;
                                    $modelAccount->owner = $modelMember->id;
                                    $modelAccount->balance = 0;
                                    $modelAccount->association = $modelAssociation->id;
                                    $modelAccount->save();
                                }

                                $modelCashOut = new CashOut();
                                $modelCashOut->member = $modelMember->id;
                                $modelCashOut->amount = $modelLoan->amount_requested + $modelLoan->interest;
                                $modelCashOut->balance_before = $modelAccount->balance;
                                $modelCashOut->balance_after = $modelCashOut->balance_before - $modelCashOut->amount;
                                $modelCashOut->status = CashOut::CASH_OUT_GIVEN;
                                $modelCashOut->payment_method = CashOut::CASH_OUT_CASH_PAYMENT;
                                $modelCashOut->phone_number = $modelMember->phone_number;
                                $modelCashOut->association = $modelAssociation->id;
                                $modelCashOut->bank_account = $modelLoan->bank_account;
                                if($modelCashOut->save()){
                                    // deduct memeber account
                                    $modelAccount->balance = $modelAccount->balance - $modelCashOut->amount;
                                    $modelAccount->save();                           
                                }

                                //perform loan refund
                                $modelLoanRefund = new LoanRefund();
                                $modelLoanRefund->loan = $modelLoan->id;
                                $modelLoanRefund->refund_date = Date('Y-m-d');
                                $modelLoanRefund->amount_given = $modelLoan->amount_requested + $modelLoan->interest;
                                $modelLoanRefund->remain_before = $modelLoan->amount_requested + $modelLoan->interest;
                                $modelLoanRefund->remain_after = 0;
                                $modelLoanRefund->association = $modelAssociation->id;
                                if($modelLoanRefund->save()){
                                    $modelLoanRefund->computeInterestsSharingAndNotifyMembers();
                                    echo "\n\n Loan added in table, djanguiMemberId = ".$modelMember->id."\n\n";
                                }

                                //send notification to the member =================================================================
                            $modelNotification = Notification::find()->where(['name' => 'contribution_delay_charges'])->one();
                                        
                            //SMS Notification
                            $modelSmsNotification = new SmsNotification();
                            $modelSmsNotification->phone_number = $modelMember->phone_number;
                            //$modelSmsNotification->content = $modelNotification->sms_content;
                            if(strstr($modelMember->language, "fr")){
                                    $modelSmsNotification->content = utf8_encode($modelNotification->sms_content_fr);
                                    Yii::$app->formatter->locale = 'fr-FR';
                                }else{
                                    $modelSmsNotification->content = $modelNotification->sms_content_en;
                                    Yii::$app->formatter->locale = 'en-US';
                                }

                            $modelSmsNotification->content = str_replace("[TODAY]", Date('Y-m-d'), $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[DJANGUI_DELAY_CHARGES]", $modelLoan->interest, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[MEMBER_BANK_SOLDE]", $modelAccount->balance, $modelSmsNotification->content);
                            $modelSmsNotification->content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelSmsNotification->content);
                            $modelSmsNotification->status = 0;
                            $modelSmsNotification->save();

                            //Email Notification
                            $modelEmailNotification = new EmailNotification();
                            $modelEmailNotification->send_to = $modelMember->email_address;
                            //$modelEmailNotification->html_content = $modelNotification->email_content;
                            if(strstr($modelMember->language, "fr")){
                                    $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                                    Yii::$app->formatter->locale = 'fr-FR';
                                }else{
                                    $modelEmailNotification->html_content = $modelNotification->email_content_en;
                                    Yii::$app->formatter->locale = 'en-US';
                                }
                            $modelEmailNotification->subject = 'eDjangui - '.Yii::t('app', 'Charges Alert');
                            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelMember->name, $modelEmailNotification->html_content); 
                            $modelEmailNotification->html_content = str_replace("[TODAY]",  Date('Y-m-d'), $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_EPISODE_NAME]", $modelEpisode->name, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_DEADLINE]", $modelEpisode->meeting_date, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_CONTRIBUTION_AMOUNT]", $modelDjangui->amount, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[DJANGUI_DELAY_CHARGES]", $modelLoan->interest, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[MEMBER_BANK_SOLDE]", $modelAccount->balance, $modelEmailNotification->html_content);
                                    $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
                            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
                            $modelEmailNotification->sending_status = 0;
                            $modelEmailNotification->save();
                            //=================================================================================================
                            }
                        }
                    }
                }
            }
        }
    }

    /**
    * Check all loans which have passed the return date, close them and created a new loan of the similar details. The amount of new loan will be equal to the remaining amount to refund from previous loan.
    *
    */
    public function actionPostponeExpiredLoans(){
        //Get all Loan which was supposed to be refunded latest yesterday.
        $modelLoansExpiredSoon = Loan::find()->where('return_date < "'.Date('Y-m-d').'"')->andWhere('status ='.Loan::LOAN_GIVEN)->all();
        foreach ($modelLoansExpiredSoon as $modelLoan) {
            //Get the remaining amount and build the refund
            $currentLoanRemainingAmount = $modelLoan->getRemainingAmountToRefund();
            $modelLoanRefund = new LoanRefund();
            $modelLoanRefund->loan = $modelLoan->id;
            $modelLoanRefund->refund_date = Date('Y-m-d');
            $modelLoanRefund->amount_given = $currentLoanRemainingAmount;
            $modelLoanRefund->remain_before = $currentLoanRemainingAmount;
            $modelLoanRefund->remain_after = 0;
            $modelLoanRefund->association = $modelLoan->association;
            $modelLoanRefund->save();

            //share interests
            $modelLoanRefund->computeInterestsSharingAndNotifyMembers();

            //create a new loan
            $modelNewLoan = new Loan();
            $modelNewLoan->taker = $modelLoan->taker;
            $modelNewLoan->amount_requested = $currentLoanRemainingAmount;
            $modelNewLoan->amount_received = $modelNewLoan->amount_requested;
            $modelNewLoan->taken_date = Date('Y-m-d');
            $modelNewLoan->loan_option = LoanOption::findOne($modelLoan->loan_option)->postpone_option;
            $modelNewLoan->status = Loan::LOAN_GIVEN;
            $modelNewLoan->payment_method = $modelLoan->payment_method;
            $modelNewLoan->phone_number = $modelLoan->phone_number;
            $modelNewLoan->association = $modelLoan->association;
            $modelNewLoan->bank_account = $modelLoan->bank_account;

            //Generate start and end date
            $modelOldLoanOption = LoanOption::findOne($modelLoan->loan_option);
            $modelLoanOptionId = $modelOldLoanOption->postpone_option == 0 ? $modelOldLoanOption->id : $modelOldLoanOption->postpone_option;
            $modelLoanOption = LoanOption::findOne($modelLoanOptionId);
            $modelNewLoan->taken_date = date('Y-m-d');
            $modelNewLoan->interest = $modelNewLoan->amount_requested * $modelLoanOption->interest_rate / 100;
            $modelNewLoan->return_date = date_create($modelNewLoan->taken_date);
            $modelNewLoan->loan_option = $modelLoanOption->id;
            date_add($modelNewLoan->return_date, date_interval_create_from_date_string(($modelLoanOption->number_of_terms*$modelLoanOption->term_duration)." days"));
            $modelNewLoan->return_date = date_format($modelNewLoan->return_date, "Y-m-d");

            //Align end date with the choosen loan return day in loan option =======================================
            if($modelLoanOption->refund_deadline <> 100){
                if((7 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 37)){
                    $firstDayOfRetrunMonth = date("Y-m-01", strtotime($modelNewLoan->return_date));
                    $d1 = date_create($firstDayOfRetrunMonth);
                    $d = date_add($d1, date_interval_create_from_date_string(($modelLoanOption->refund_deadline - 7)." days"));
                    $modelNewLoan->return_date = date_format($d, "Y-m-d");

                }elseif ((38 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 79)) {
                    if (($modelLoanOption->refund_deadline - 38) % 6  == 0) $rank = "first";
                    if (($modelLoanOption->refund_deadline - 39) % 6  == 0) $rank = "second";
                    if (($modelLoanOption->refund_deadline - 40) % 6  == 0) $rank = "third";
                    if (($modelLoanOption->refund_deadline - 41) % 6  == 0) $rank = "fouth";

                    if (($modelLoanOption->refund_deadline - 42) % 6  == 0) $rank = "last";
                    if (($modelLoanOption->refund_deadline - 43) % 6  == 0) $rank = "last";

                    if((38 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 43)) $day_of_week = "monday";
                    if((44 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 49)) $day_of_week = "tuesday";
                    if((50 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 55)) $day_of_week = "wednesday";
                    if((56 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 61)) $day_of_week = "thursday";
                    if((62 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 67)) $day_of_week = "friday";
                    if((68 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 73)) $day_of_week = "saturday";
                    if((74 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 79)) $day_of_week = "sunday";

                    $monthOfReturnDate = $firstDayOfRetrunMonth = date("Y-m-01", strtotime($modelNewLoan->return_date->format('Y-m-d')));
                    $modelNewLoan->return_date = date("Y-m-d", strtotime($rank." ".$day_of_week." ".$monthOfReturnDate));

                    //get before last week day of given date
                    if (($modelLoanOption->refund_deadline - 42) % 6  == 0){
                        $d2 = date_create($modelNewLoan->return_date);
                        date_sub($d2,date_interval_create_from_date_string("7 days"));
                        $modelNewLoan->return_date = date_format($d2,"Y-m-d");
                    }
                }
            }
            //====================================================================================================
            $modelNewLoan->save();

            //create endorser
            $modelNewLoanEndorse = new LoanEndorse();
            $modelLoanEndorse = LoanEndorse::find()->where(['loan' => $modelLoan->id, 'status' => LoanEndorse::LOAN_ENDORSE_APPROVED])->one();
            if(!is_null($modelLoanEndorse)){
                $modelNewLoanEndorse->loan = $modelNewLoan->id;
                $modelNewLoanEndorse->endorser = $modelLoanEndorse->endorser;
                $modelNewLoanEndorse->endorse_amount = $modelLoanEndorse->endorse_amount;
                $modelNewLoanEndorse->status = LoanEndorse::LOAN_ENDORSE_APPROVED;
                $modelNewLoanEndorse->save();
            }

            //initialise interest sharing
            $modelNewLoan->initializeInterestsAndShare();

            // Send notification the the loan taker ==============================================================================
            //build the messages to be sent
            $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_expired_loan_postponed'])->one();
            $modelUser = User::findOne(['id' => $modelNewLoan->taker]);
            $modelAssociation = Association::findOne($modelUser->association);
                        
            //SMS Notification
            $modelSmsNotification = new SmsNotification();
            $modelSmsNotification->phone_number = $modelAssociation->admin_phone_number;
            //$modelSmsNotification->content = $modelNotification->sms_content_en;
            if(strstr($modelUser->language, "fr")){
                    $modelSmsNotification->content = $modelNotification->sms_content_fr;
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelSmsNotification->content = $modelNotification->sms_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelNewLoan->amount_requested + $modelNewLoan->interest), $modelSmsNotification->content);            
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_RETURN_DATE]", Yii::$app->formatter->asDate($modelNewLoan->return_date, "php: M-d-Y"), $modelSmsNotification->content);
            $modelSmsNotification->status = 0;
            $modelSmsNotification->save();

            //Email Notification
            $modelEmailNotification = new EmailNotification();
            $modelEmailNotification->send_to = $modelUser->email_address;
            //$modelEmailNotification->html_content = $modelNotification->email_content;
            if(strstr($modelUser->language, "fr")){
                    $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelEmailNotification->html_content = $modelNotification->email_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelEmailNotification->subject = 'eDjangui - Loan Postpone';
            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelNewLoan->amount_requested + $modelNewLoan->interest), $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[PREVIOUS_LOAN_RETURN_DATE]", Yii::$app->formatter->asDate($modelLoan->return_date), $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_RETURN_DATE]", Yii::$app->formatter->asDate($modelNewLoan->return_date), $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
            $modelEmailNotification->sending_status = 0;
            $modelEmailNotification->save();
            //====================================================================================================================


            print_r("\n".Date('Y-m-d H:i:s')." LOAN ".$modelLoan->id." POSPONED"."\n");
        }
    }


    /**
    * Check all loans which are supposed toe be reimburse during current episode and send reminder to the loan takers. This is the notification of the 1st day of the season.
    *
    */
    public function actionSendLoanReimbursementRemiderDaysBefore(){
        //echo "strcmp ".strcmp($rows[0]['start_date'], Date('Y-m-d'));

        
        $modelLoansExpiredSoon = Loan::find()
            ->where('return_date = DATE_ADD(CURDATE(), INTERVAL 2 DAY)')
            ->andWhere(['status' => Loan::LOAN_GIVEN])
            ->all();

        //echo 'return_date = "'.date('Y-m-d', strtotime($rows[0]['start_date']. ' + '.($modelSeason->loan_return_day - 1).' days')).'"';
        foreach ($modelLoansExpiredSoon as $modelLoan) {

            // Send notification the the loan taker ==============================================================================
            //build the messages to be sent
            $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_expiry_date_close'])->one();
            $modelUser = User::findOne(['id' => $modelLoan->taker]);
            $modelAssociation = Association::findOne($modelLoan->association);
                        
            //SMS Notification
            $modelSmsNotification = new SmsNotification();
            $modelSmsNotification->phone_number = $modelUser->phone_number;
            //$modelSmsNotification->content = $modelNotification->sms_content_en;
            if(strstr($modelUser->language, "fr")){
                    $modelSmsNotification->content = $modelNotification->sms_content_fr;
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelSmsNotification->content = $modelNotification->sms_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->getFirstAndSecondNames(), $modelSmsNotification->content);
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_AMOUNT]", $modelLoan->getRemainingAmountToRefund(), $modelSmsNotification->content);            
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_RETURN_DATE]", $modelLoan->return_date, $modelSmsNotification->content);
            $modelSmsNotification->status = 0;
            $modelSmsNotification->save();


            //Email Notification
            $modelEmailNotification = new EmailNotification();
            $modelEmailNotification->send_to = $modelUser->email_address;
            //$modelEmailNotification->html_content = $modelNotification->email_content;
            if(strstr($modelUser->language, "fr")){
                    $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelEmailNotification->html_content = $modelNotification->email_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelEmailNotification->subject = 'eDjangui - Loan Deadline Reminder';
            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_AMOUNT]", $modelLoan->getRemainingAmountToRefund(), $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_RETURN_DATE]", $modelLoan->return_date, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
            $modelEmailNotification->sending_status = 0;
            $modelEmailNotification->save();
            //====================================================================================================================


            print_r("\n".Date('Y-m-d H:i:s')." LOAN ".$modelLoan->id." REMINDER"."\n");
        }
    }



    /**
    * Check all loans which are supposed toe be reimburse during current episode and send reminder to the loan takers. This is the notification on the Deadline Day.
    *
    */
    public function actionSendLoanReimbursementRemiderLastDay(){
        $modelLoansExpiredSoon = Loan::find()
            ->where('return_date = CURDATE()')
            ->andWhere(['status' => Loan::LOAN_GIVEN])
            ->all();

        //echo 'return_date = "'.date('Y-m-d', strtotime($rows[0]['start_date']. ' + '.($modelSeason->loan_return_day - 1).' days')).'"';
        foreach ($modelLoansExpiredSoon as $modelLoan) {

            // Send notification the the loan taker ==============================================================================
            //build the messages to be sent
            $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_expiry_date_reached'])->one();
            $modelUser = User::findOne(['id' => $modelLoan->taker]);
            $modelAssociation = Association::findOne($modelLoan->association);
                        
            //SMS Notification
            $modelSmsNotification = new SmsNotification();
            $modelSmsNotification->phone_number = $modelUser->phone_number;
            //$modelSmsNotification->content = $modelNotification->sms_content_en;
            if(strstr($modelUser->language, "fr")){
                    $modelSmsNotification->content = $modelNotification->sms_content_fr;
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelSmsNotification->content = $modelNotification->sms_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->getFirstAndSecondNames(), $modelSmsNotification->content);
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_AMOUNT]", $modelLoan->getRemainingAmountToRefund(), $modelSmsNotification->content);            
            $modelSmsNotification->content = str_replace("[CURRENT_LOAN_RETURN_DATE]", $modelLoan->return_date, $modelSmsNotification->content);
            $modelSmsNotification->status = 0;
            $modelSmsNotification->save();


            //Email Notification
            $modelEmailNotification = new EmailNotification();
            $modelEmailNotification->send_to = $modelUser->email_address;
            //$modelEmailNotification->html_content = $modelNotification->email_content;
            if(strstr($modelUser->language, "fr")){
                    $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelEmailNotification->html_content = $modelNotification->email_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
            $modelEmailNotification->subject = 'eDjangui - Loan Deadline Reminder';
            $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_AMOUNT]", $modelLoan->getRemainingAmountToRefund(), $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[CURRENT_LOAN_RETURN_DATE]", $modelLoan->return_date, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelAssociation->admin_phone_number, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelAssociation->admin_email_address, $modelEmailNotification->html_content);
            $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
            $modelEmailNotification->sending_status = 0;
            $modelEmailNotification->save();
            //====================================================================================================================


            print_r("\n".Date('Y-m-d H:i:s')." LOAN ".$modelLoan->id." REMINDER"."\n");
        }
    }
}

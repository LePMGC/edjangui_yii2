<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_option".
 *
 * @property int $id
 * @property int $association
 * @property string|null $name
 * @property float|null $min_amount
 * @property float|null $max_amount
 * @property float|null $interest_rate
 * @property int|null $number_of_terms
 * @property int|null $term_duration
 * @property int|null $postpone_option
 * @property int|null $postpone_capital
 * @property int|null $bank_account
 * @property int|null $refund_deadline
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property LoanOptionTerm[] $loanOptionTerms
 * @property LoanOption[] $loanOptions
 * @property LoanOption $postponeOption
 * @property User $updatedBy
 */
class LoanOption extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_account'], 'required'],
            [['association', 'number_of_terms', 'term_duration', 'postpone_option', 'postpone_capital', 'created_by', 'updated_by', 'bank_account', 'refund_deadline'], 'integer'],
            [['association', 'bank_account', 'min_amount', 'max_amount'], 'unique', 'targetAttribute' => ['min_amount', 'max_amount']],
            [['min_amount', 'max_amount', 'interest_rate'], 'number', 'min' => 0],
            //['max_amount', 'compare', 'compareAttribute'=> 'min_amount',  'operator' => ">"],
            [['created_on', 'updated_on'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            //[['postpone_option'], 'exist', 'skipOnError' => true, 'targetClass' => LoanOption::class, 'targetAttribute' => ['postpone_option' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'association' => Yii::t('app', 'Association'),
            'name' => Yii::t('app', 'Name'),
            'min_amount' => Yii::t('app', 'Min Amount'),
            'max_amount' => Yii::t('app', 'Max Amount'),
            'interest_rate' => Yii::t('app', 'Interest Rate'),
            'number_of_terms' => Yii::t('app', 'Number Of Terms'),
            'term_duration' => Yii::t('app', 'Term Duration'),
            'postpone_option' => Yii::t('app', 'Postpone Option'),
            'postpone_capital' => Yii::t('app', 'Postpone Capital'),
            'bank_account' => Yii::t('app', 'Bank Account'),
            'refund_deadline' => Yii::t('app', 'Refund Deadline'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * Gets query for [[Association0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociation0()
    {
        return $this->hasOne(Association::class, ['id' => 'association']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[LoanOptionTerms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptionTerms()
    {
        return $this->hasMany(LoanOptionTerm::class, ['loan_option' => 'id']);
    }

    /**
     * Gets query for [[LoanOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptions()
    {
        return $this->hasMany(LoanOption::class, ['postpone_option' => 'id']);
    }

    /**
     * Gets query for [[PostponeOption]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostponeOption()
    {
        return $this->hasOne(LoanOption::class, ['id' => 'postpone_option']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }


    /**
    * Get list of available loan duration terms
    */
    public static function getListOfAllTermDurations(){
        return array(
            1 => Yii::t('app', 'Daily'),
            7 => Yii::t('app', 'Weekly'),
            30 => Yii::t('app', 'Monthly')
        );
    }


    /**
    * Get list of available loan postpone option
    */
    public static function getListOfAllPostponeOptions(){
        $results = array();
        $results[0] = Yii::t('app', 'This Option');

        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $allLoanOptionModels = LoanOption::find()->where("id>0 and association=".$modelCurrentUser->association)->all();
        foreach ($allLoanOptionModels as $loanOptionModel) {
            $results[$loanOptionModel->id] = $loanOptionModel->name;
        }

        return $results;
    }


    /**
    * Get list of available loan postpone capitals
    */
    public static function getListOfAllPostponeCapital(){
        $results = array();
        $results[0] = Yii::t('app', 'Capital');
        $results[1] = Yii::t('app', 'Capital + Interests');

        return $results;
    }

    /**
    * Check if the choosen interval is OK
    */
    public function isGivenIntervalOk(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $allLoanOptionModels = LoanOption::find()->where("id>0 and association=".$modelCurrentUser->association)->all();
        $i = 0;
        $numberOfLoanOptions = count($allLoanOptionModels);
        $isGivenIntervalOk = true;
        while (($i < $numberOfLoanOptions) && $isGivenIntervalOk) {
            if(($allLoanOptionModels[$i]->min_amount <= $this->min_amount) && ($this->min_amount <= $allLoanOptionModels[$i]->max_amount)){
                $isGivenIntervalOk = false;
                $this->addError("min_amount", Yii::t('app', 'The Choosen Interval is part of the one choosen for').' "'.$allLoanOptionModels[$i]->name.'"');
            }

            if(($allLoanOptionModels[$i]->min_amount <= $this->min_amount) && ($this->max_amount <= $allLoanOptionModels[$i]->max_amount)){
                $isGivenIntervalOk = false;
                $this->addError("min_amount", Yii::t('app', 'The Choosen Interval is part of the one choosen for').' "'.$allLoanOptionModels[$i]->name.'"');
            }

            $i++;
        }
        
        return $isGivenIntervalOk;
    }

    /**
    * Get list of all refund day type
    */
    public static function getListOfAllRefundDayTypes(){
        $results = array(
            100 => Yii::t('app', 'End Of Terms'),
            "Week Day" => array(
                0 => Yii::t('app', 'Monday'),
                1 => Yii::t('app', 'Tuesday'),
                2 => Yii::t('app', 'Wednesday'),
                3 => Yii::t('app', 'Thursday'),
                4 => Yii::t('app', 'Friday'),
                5 => Yii::t('app', 'Saturday'),
                6 => Yii::t('app', 'Sunday'),
            ),
            "Month Day" => array(
                7 => Yii::t('app', '1'),
                8 => Yii::t('app', '2'),
                9 => Yii::t('app', '3'),
                10 => Yii::t('app', '4'),
                11 => Yii::t('app', '5'),
                12 => Yii::t('app', '6'),
                13 => Yii::t('app', '7'),
                14 => Yii::t('app', '8'),
                15 => Yii::t('app', '9'),
                16 => Yii::t('app', '10'),
                17 => Yii::t('app', '11'),
                18 => Yii::t('app', '12'),
                19 => Yii::t('app', '13'),
                20 => Yii::t('app', '14'),
                21 => Yii::t('app', '15'),
                22 => Yii::t('app', '16'),
                23 => Yii::t('app', '17'),
                24 => Yii::t('app', '18'),
                25 => Yii::t('app', '19'),
                26 => Yii::t('app', '20'),
                27 => Yii::t('app', '21'),
                28 => Yii::t('app', '22'),
                29 => Yii::t('app', '23'),
                30 => Yii::t('app', '24'),
                31 => Yii::t('app', '25'),
                32 => Yii::t('app', '26'),
                33 => Yii::t('app', '27'),
                34 => Yii::t('app', '28'),
                35 => Yii::t('app', '29'),
                36 => Yii::t('app', '30'),
                37 => Yii::t('app', '31'),
            ),
            "Week Day Of the Month" => array(
                38 => Yii::t('app', '1st Monday'),
                39 => Yii::t('app', '2nd Monday'),
                40 => Yii::t('app', '3rd Monday'),
                41 => Yii::t('app', '4th Monday'),
                42 => Yii::t('app', 'One Before Last Monday'),
                43 => Yii::t('app', 'Last Monday'),
                44 => Yii::t('app', '1st Tuesday'),
                45 => Yii::t('app', '2nd Tuesday'),
                46 => Yii::t('app', '3rd Tuesday'),
                47 => Yii::t('app', '4th Tuesday'),
                48 => Yii::t('app', 'One Before Last Tuesday'),
                49 => Yii::t('app', 'Last Tuesday'),
                50 => Yii::t('app', '1st Wednesday'),
                51 => Yii::t('app', '2nd Wednesday'),
                52 => Yii::t('app', '3rd Wednesday'),
                53 => Yii::t('app', '4th Wednesday'),
                54 => Yii::t('app', 'One Before Last Wednesday'),
                55 => Yii::t('app', 'Last Wednesday'),
                56 => Yii::t('app', '1st Thursday'),
                57 => Yii::t('app', '2nd Thursday'),
                58 => Yii::t('app', '3rd Thursday'),
                59 => Yii::t('app', '4th Thursday'),
                60 => Yii::t('app', 'One Before Last Thursday'),
                61 => Yii::t('app', 'Last Thursday'),
                62 => Yii::t('app', '1st Friday'),
                63 => Yii::t('app', '2nd Friday'),
                64 => Yii::t('app', '3rd Friday'),
                65 => Yii::t('app', '4th Friday'),
                66 => Yii::t('app', 'One Before Last Friday'),
                67 => Yii::t('app', 'Last Friday'),
                68 => Yii::t('app', '1st Satuday'),
                69 => Yii::t('app', '2nd Satuday'),
                70 => Yii::t('app', '3rd Satuday'),
                71 => Yii::t('app', '4th Satuday'),
                72 => Yii::t('app', 'One Before Last Satuday'),
                73 => Yii::t('app', 'Last Satuday'),
                74 => Yii::t('app', '1st Sunday'),
                75 => Yii::t('app', '2nd Sunday'),
                76 => Yii::t('app', '3rd Sunday'),
                77 => Yii::t('app', '4th Sunday'),
                78 => Yii::t('app', 'One Before Last Sunday'),
                79 => Yii::t('app', 'Last Sunday'),
            ),
        );
        return $results;
    }

    public static function getNameOfRedundDeadline($refundDeadlineId){
        switch ($refundDeadlineId) {
                case 100: return Yii::t('app', 'End Of Terms'); break;
                case 0: return Yii::t('app', 'Monday'); break;
                case 1: return Yii::t('app', 'Tuesday'); break;
                case 2: return Yii::t('app', 'Wednesday'); break;
                case 3: return Yii::t('app', 'Thursday'); break;
                case 4: return Yii::t('app', 'Friday'); break;
                case 5: return Yii::t('app', 'Saturday'); break;
                case 6: return Yii::t('app', 'Sunday'); break;
                case 7: return Yii::t('app', 'Month 1'); break;
                case 8: return Yii::t('app', 'Month 2'); break;
                case 9: return Yii::t('app', 'Month 3'); break;
                case 10: return Yii::t('app', 'Month 4'); break;
                case 11: return Yii::t('app', 'Month 5'); break;
                case 12: return Yii::t('app', 'Month 6'); break;
                case 13: return Yii::t('app', 'Month 7'); break;
                case 14: return Yii::t('app', 'Month 8'); break;
                case 15: return Yii::t('app', 'Month 9'); break;
                case 16: return Yii::t('app', 'Month 10'); break;
                case 17: return Yii::t('app', 'Month 11'); break;
                case 18: return Yii::t('app', 'Month 12'); break;
                case 19: return Yii::t('app', 'Month 13'); break;
                case 20: return Yii::t('app', 'Month 14'); break;
                case 21: return Yii::t('app', 'Month 15'); break;
                case 22: return Yii::t('app', 'Month 16'); break;
                case 23: return Yii::t('app', 'Month 17'); break;
                case 24: return Yii::t('app', 'Month 18'); break;
                case 25: return Yii::t('app', 'Month 19'); break;
                case 26: return Yii::t('app', 'Month 20'); break;
                case 27: return Yii::t('app', 'Month 21'); break;
                case 28: return Yii::t('app', 'Month 22'); break;
                case 29: return Yii::t('app', 'Month 23'); break;
                case 30: return Yii::t('app', 'Month 24'); break;
                case 31: return Yii::t('app', 'Month 25'); break;
                case 32: return Yii::t('app', 'Month 26'); break;
                case 33: return Yii::t('app', 'Month 27'); break;
                case 34: return Yii::t('app', 'Month 28'); break;
                case 35: return Yii::t('app', 'Month 29'); break;
                case 36: return Yii::t('app', 'Month 30'); break;
                case 37: return Yii::t('app', 'Month 31'); break;
                case 38: return Yii::t('app', '1st Monday'); break;
                case 39: return Yii::t('app', '2nd Monday'); break;
                case 40: return Yii::t('app', '3rd Monday'); break;
                case 41: return Yii::t('app', '4th Monday'); break;
                case 42: return Yii::t('app', 'One Before Last Monday'); break;
                case 43: return Yii::t('app', 'Last Monday'); break;
                case 44: return Yii::t('app', '1st Tuesday'); break;
                case 45: return Yii::t('app', '2nd Tuesday'); break;
                case 46: return Yii::t('app', '3rd Tuesday'); break;
                case 47: return Yii::t('app', '4th Tuesday'); break;
                case 48: return Yii::t('app', 'One Before Last Tuesday'); break;
                case 49: return Yii::t('app', 'Last Tuesday'); break;
                case 50: return Yii::t('app', '1st Wednesday'); break;
                case 51: return Yii::t('app', '2nd Wednesday'); break;
                case 52: return Yii::t('app', '3rd Wednesday'); break;
                case 53: return Yii::t('app', '4th Wednesday'); break;
                case 54: return Yii::t('app', 'One Before Last Wednesday'); break;
                case 55: return Yii::t('app', 'Last Wednesday'); break;
                case 56: return Yii::t('app', '1st Thursday'); break;
                case 57: return Yii::t('app', '2nd Thursday'); break;
                case 58: return Yii::t('app', '3rd Thursday'); break;
                case 59: return Yii::t('app', '4th Thursday'); break;
                case 60: return Yii::t('app', 'One Before Last Thursday'); break;
                case 61: return Yii::t('app', 'Last Thursday'); break;
                case 62: return Yii::t('app', '1st Friday'); break;
                case 63: return Yii::t('app', '2nd Friday'); break;
                case 64: return Yii::t('app', '3rd Friday'); break;
                case 65: return Yii::t('app', '4th Friday'); break;
                case 66: return Yii::t('app', 'One Before Last Friday'); break;
                case 67: return Yii::t('app', 'Last Friday'); break;
                case 68: return Yii::t('app', '1st Satuday'); break;
                case 69: return Yii::t('app', '2nd Satuday'); break;
                case 70: return Yii::t('app', '3rd Satuday'); break;
                case 71: return Yii::t('app', '4th Satuday'); break;
                case 72: return Yii::t('app', 'One Before Last Satuday'); break;
                case 73: return Yii::t('app', 'Last Satuday'); break;
                case 74: return Yii::t('app', '1st Sunday'); break;
                case 75: return Yii::t('app', '2nd Sunday'); break;
                case 76: return Yii::t('app', '3rd Sunday'); break;
                case 77: return Yii::t('app', '4th Sunday'); break;
                case 78: return Yii::t('app', 'One Before Last Sunday'); break;
                case 79: return Yii::t('app', 'Last Sunday'); break;            
            default:
                # code...
                break;
        }
    }

     /**
     * {@inheritdoc}
     * initiallize the loan options items
     */
    public function initiallizeTerms(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        for ($i=0; $i < $this->number_of_terms; $i++) { 
            $modelLoanOptionTerm = new LoanOptionTerm();
            $modelLoanOptionTerm->name = $this->name." - #".($i+1);
            $modelLoanOptionTerm->rank = $i+1;
            $modelLoanOptionTerm->amount_to_refund = 1;
            $modelLoanOptionTerm->percentage = 100/$this->number_of_terms;
            $modelLoanOptionTerm->association = $modelCurrentUser->association;
            $modelLoanOptionTerm->loan_option = $this->id;
            $modelLoanOptionTerm->save();
        }
    } 
}
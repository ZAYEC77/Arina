<?php

/**
 * This is the model class for table "sp_subject".
 *
 * The followings are the available columns in table 'sp_subject':
 * @property integer $id
 * @property integer $plan_id
 * @property integer $subject_id
 * @property integer $total
 * @property integer $lectures
 * @property integer $labs
 * @property integer $practs
 * @property array $weeks
 * @property array $control
 *
 * The followings are the available model relations:
 * @property StudyPlan $plan
 * @property Subject $subject
 */
class StudySubject extends ActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sp_subject';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('plan_id, subject_id, total', 'required', 'message' => 'Вкажіть {attribute}'),
            array('weeks', 'check_weeks'),
            array('plan_id, subject_id, total, lectures, labs, practs', 'numerical', 'integerOnly' => true),
            array('total', 'check_hours'),
            array('lectures', 'check_classes'),
            array('lectures, labs, practs', 'default', 'value' => 0, 'on' => 'insert'),
            array('plan_id, subject_id, total, lectures, labs, practs, weeks', 'safe'),
            array('id, plan_id, subject_id, total, lectures, labs, practs, subject', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'plan' => array(self::BELONGS_TO, 'StudyPlan', 'plan_id'),
            'subject' => array(self::BELONGS_TO, 'Subject', 'subject_id'),
        );
    }

    public function behaviors()
    {
        return array(
            'StrBehavior' => array(
                'class' => 'application.behaviors.StrBehavior',
                'fields' => array(
                    'weeks',

                ),
            ),
            'JSONBehavior' => array(
                'class' => 'application.behaviors.JSONBehavior',
                'fields' => array(
                    'control',
                ),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'plan_id' => 'Plan',
            'subject_id' => Yii::t('terms', 'Subject'),
            'total' => 'Загальна кількість',
            'lectures' => 'Лекції',
            'labs' => 'Лабораторні',
            'practs' => 'Практичні',
            'classes' => 'Всього аудиторних',
            'selfwork' => 'Самостійна робота',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('plan_id', $this->plan_id);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->with('subject');
        $criteria->compare('subject.title', $this->subject_id, true);

        $sort = new CSort();
        $sort->attributes = array(
            'subject_id' => array(
                'asc' => 'subject.title ASC',
                'desc' => 'subject.title DESC',
            ),
        );
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return StudySubject the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return int
     */
    public function getClasses()
    {
        return $this->lectures + $this->practs + $this->labs;
    }

    /**
     * @return int
     */
    public function getSelfwork()
    {
        return $this->total - $this->getClasses();
    }

    /**
     * @param $semester
     * @return string
     */
    public function getWeeklyHours($semester)
    {
        return isset($this->weeks[$semester]) ? $this->weeks[$semester] : '';
    }

    public function check_hours()
    {
        if (!$this->hasErrors()) {
            if ($this->total < ($this->lectures + $this->labs + $this->practs))
                $this->addError('total', 'Аудиторних годин більше ніж загальна кількість');
        }
    }

    public function check_classes()
    {
        if (!$this->hasErrors()) {
            $sum = 0;
            foreach ($this->weeks as $semester => $weekly)
                if (!empty($weekly)) {
                    $sum += $weekly * $this->plan->semesters[$semester];
                }
            if ($sum < $this->getClasses())
                $this->addError('lectures', 'Невистачає годин на тиждень для вичитки');
        }
    }

    public function check_weeks()
    {
        if (!$this->hasErrors()) {
            $valid = false;
            foreach ($this->weeks as $week)
                if (!empty($week))
                    $valid = true;
            if (!$valid)
                $this->addError('weeks', 'Вкажіть кількість годин на тиждень у відповідних семестрах');
        }
    }
}
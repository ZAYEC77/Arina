<?php

/**
 * This is the model class for table "group".
 *
 * The followings are the available columns in table 'group':
 * @property integer $id
 * @property string $title
 * @property integer $speciality_id
 * @property integer $curator_id
 * @property integer $monitor_id
 *
 * @property Student[] $students
 * @property Speciality $speciality
 */
class Group extends ActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Group the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getCourse($yearId = null)
    {
        $year = null;
        if (isset($yearId)) {
            $year = StudyYear::model()->findByPk($yearId);
        }
        if (!isset($year)) {
            $year = StudyYear::getCurrentYear();
        }
        $last_year = mb_substr($this->title, 3, 2, 'UTF-8');
        $value = $year->end - 2000 - $last_year;
        return $value;
    }

    public function getStudentsList()
    {
        return CHtml::listData($this->students, 'id', 'fullName');
    }

    public static function getTreeList()
    {
        $list = array();
        /**
         * @var $speciality Speciality[]
         */
        $speciality = Speciality::model()->findAll();
        foreach ($speciality as $item) {
            $list[$item->title] = array();
            foreach ($item->groups as $group) {
                $list[$item->title][$group->id] = $group->title;
            }
        }
        return $list;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title, speciality_id, curator_id', 'required'),
            array('monitor_id', 'required', 'on' => 'update'),
            array('speciality_id, curator_id, monitor_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 8),
            array('title', 'unique'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'speciality' => array(self::BELONGS_TO, 'Speciality', 'speciality_id'),
            'curator' => array(self::BELONGS_TO, 'Teacher', 'curator_id'),
            'students' => array(self::HAS_MANY, 'Student', 'group_id', 'order' => 'last_name, first_name, middle_name ASC'),
            'loads' => array(self::HAS_MANY, 'TeacherLoad', 'group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('base', 'ID'),
            'title' => Yii::t('base', 'Title'),
            'speciality_id' => Yii::t('base', 'Speciality'),
            'curator_id' => Yii::t('group', 'Curator'),
            'curator' => Yii::t('group', 'Curator'),
            'monitor_id' => Yii::t('group', 'Monitor'),
        );
    }

    private $curator_old;
    private $monitor_old;

    protected function beforeSave()
    {

        if ($this->curator_id != $this->curator_old) {
            $auth = Yii::app()->authManager;
            $curator_old_user = User::model()->findByAttributes(
                array(
                    'identity_id' => $this->curator_old,
                    'identity_type' => User::TYPE_TEACHER
                )
            );
            $curator_new_user = User::model()->findByAttributes(
                array(
                    'identity_id' => $this->curator_id,
                    'identity_type' => User::TYPE_TEACHER
                )
            );
            if (isset($curator_old_user)) {
                $auth->revoke('curator', $curator_old_user->getAttribute('id'));
            }
            if (isset($curator_new_user)) {
                $auth->assign('curator', $curator_new_user->getAttribute('id'));
            }

            $monitor_old_user = User::model()->findByAttributes(
                array(
                    'identity_id' => $this->monitor_old,
                    'identity_type' => User::TYPE_STUDENT
                )
            );
            $monitor_new_user = User::model()->findByAttributes(
                array(
                    'identity_id' => $this->monitor_id,
                    'identity_type' => User::TYPE_STUDENT
                )
            );
            if (isset($monitor_old_user)) {
                $auth->revoke('prefect', $monitor_old_user->getAttribute('id'));
            }
            if (isset($monitor_new_user)) {
                $auth->assign('prefect', $monitor_new_user->getAttribute('id'));
            }
        }

        return parent::beforeSave();
    }

    protected function afterFind()
    {
        $this->curator_old = $this->curator_id;
        $this->monitor_old = $this->monitor_id;
        parent::afterFind();
    }

    /**
     * @return int
     */
    public function getStudentsCount()
    {
        return count($this->students);
    }

    /**
     * @return int
     */
    public function getBudgetStudentsCount()
    {
        $command = Yii::app()->db->createCommand();
        return $command->select('count(id)')->from('student')->where('((contract=0) OR (contract IS NULL)) AND group_id=:group', array(':group' => $this->id))->queryScalar();
    }

    /**
     * @return int
     */
    public function getContractStudentsCount()
    {
        $command = Yii::app()->db->createCommand();
        return $command->select('count(id)')->from('student')->where('contract=1 AND group_id=:group', array(':group' => $this->id))->queryScalar();
    }
}

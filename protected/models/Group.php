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
 */
class Group extends ActiveRecord
{
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
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, speciality_id, curator_id, monitor_id', 'required'),
            array('speciality_id, curator_id, monitor_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 8),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            //array('id, title, speciality_id, curator_id, monitor_id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'curator' => array(self::BELONGS_TO, 'Teacher', 'curator_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => Yii::t('group', 'Title'),
            'speciality_id' => Yii::t('group', 'Speciality'),
            'curator_id' => Yii::t('group', 'Curator'),
            'curator' => Yii::t('group', 'Curator'),
            'monitor_id' => Yii::t('group', 'Monitor'),
        );
    }

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
}

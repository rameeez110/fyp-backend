<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $name
 * @property string $ep_num
 * @property string $degree_program
 * @property string $enrolment_no
 * @property string $profile_pic
 * @property string $enrolled_date
 * @property string $status
 * @property string $section
 * @property string $meta
 * @property string $cgpa
 * @property int $user_id
 * @property string $yaer
 *
 * @property Follow[] $follows
 * @property User $user
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ep_num', 'degree_program', 'enrolment_no', 'enrolled_date', 'status', 'section', 'user_id', 'yaer'], 'required'],
            [['user_id'], 'integer'],
            [['name', 'ep_num', 'degree_program', 'enrolment_no', 'enrolled_date', 'status', 'section', 'meta', 'cgpa', 'yaer'], 'string', 'max' => 100],
            [['profile_pic'], 'string', 'max' => 1000],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ep_num' => 'Ep Num',
            'degree_program' => 'Degree Program',
            'enrolment_no' => 'Enrolment No',
            'profile_pic' => 'Profile Pic',
            'enrolled_date' => 'Enrolled Date',
            'status' => 'Status',
            'section' => 'Section',
            'meta' => 'Meta',
            'cgpa' => 'Cgpa',
            'user_id' => 'User ID',
            'yaer' => 'Yaer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollows()
    {
        return $this->hasMany(Follow::className(), ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $name
 * @property int $number
 * @property string $status
 * @property string $semester
 * @property string $program
 * @property string $meta
 * @property string $code
 *
 * @property Time[] $times
 */
class Course extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'number', 'status', 'semester', 'program', 'code', 'credit_hours'], 'required'],
            [['number'], 'integer'],
            [['status', 'semester', 'program', 'meta'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['code', 'credit_hours'], 'string', 'max' => 100],
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
            'number' => 'Number',
            'status' => 'Status',
            'semester' => 'Semester',
            'program' => 'Program',
            'meta' => 'Meta',
            'code' => 'Code',
            'credit_hours' => 'Credit Hours',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimes()
    {
        return $this->hasMany(Time::className(), ['course_id' => 'id']);
    }
}

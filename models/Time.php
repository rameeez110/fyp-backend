<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $course_id
 * @property string $date
 * @property string $day
 * @property string $meta
 * @property string $program
 * @property string $year
 * @property string $status
 * @property string $time_duration
 * @property string $is_morning
 * @property string $semester
 * @property string $is_theory
 * @property string $section
 *
 * @property Course $course
 * @property Teacher $teacher
 */
class Time extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teacher_id', 'course_id', 'date', 'day', 'program', 'year', 'status', 'time_duration', 'is_morning', 'semester', 'is_theory', 'section'], 'required'],
            [['teacher_id', 'course_id'], 'integer'],
            [['date', 'day', 'meta', 'program', 'year', 'status'], 'string', 'max' => 50],
            [['time_duration', 'is_morning', 'semester', 'is_theory', 'section'], 'string', 'max' => 100],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'course_id' => 'Course ID',
            'date' => 'Date',
            'day' => 'Day',
            'meta' => 'Meta',
            'program' => 'Program',
            'year' => 'Year',
            'status' => 'Status',
            'time_duration' => 'Time Duration',
            'is_morning' => 'Is Morning',
            'semester' => 'Semester',
            'is_theory' => 'Is Theory',
            'section' => 'Section',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['id' => 'teacher_id']);
    }
}

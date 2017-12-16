<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attachment".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $meta
 * @property string $description
 * @property int $teacher_id
 * @property string $is_result
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 * @property string $title
 *
 * @property Teacher $teacher
 */
class Attachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'teacher_id', 'is_result', 'created_at', 'updated_at', 'status', 'title'], 'required'],
            [['teacher_id'], 'integer'],
            [['name', 'type', 'meta', 'description'], 'string', 'max' => 255],
            [['is_result', 'created_at', 'updated_at'], 'string', 'max' => 50],
            [['status', 'title'], 'string', 'max' => 100],
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
            'name' => 'Name',
            'type' => 'Type',
            'meta' => 'Meta',
            'description' => 'Description',
            'teacher_id' => 'Teacher ID',
            'is_result' => 'Is Result',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['id' => 'teacher_id']);
    }
}

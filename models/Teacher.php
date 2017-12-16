<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "teacher".
 *
 * @property int $id
 * @property string $name
 * @property string $profile_pic
 * @property int $user_id
 * @property string $qualification
 * @property string $status
 * @property string $availablity
 * @property string $meta
 *
 * @property Announcement[] $announcements
 * @property Attachment[] $attachments
 * @property Follow[] $follows
 * @property User $user
 * @property Time[] $times
 */
class Teacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'user_id', 'qualification', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['name', 'qualification', 'status', 'availablity'], 'string', 'max' => 50],
            [['profile_pic'], 'string', 'max' => 1000],
            [['meta'], 'string', 'max' => 100],
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
            'profile_pic' => 'Profile Pic',
            'user_id' => 'User ID',
            'qualification' => 'Qualification',
            'status' => 'Status',
            'availablity' => 'Availablity',
            'meta' => 'Meta',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnnouncements()
    {
        return $this->hasMany(Announcement::className(), ['teacher_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::className(), ['teacher_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollows()
    {
        return $this->hasMany(Follow::className(), ['teacher_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimes()
    {
        return $this->hasMany(Time::className(), ['teacher_id' => 'id']);
    }
}

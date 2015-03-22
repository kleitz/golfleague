<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "registration".
 *
 * @property integer $id
 * @property integer $competition_id
 * @property integer $golfer_id
 * @property string $status
 * @property integer $team_id
 * @property integer $flight_id
 * @property integer $tees_id
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property integer $score
 * @property integer $score_net
 * @property integer $points
 * @property integer $position
 *
 * @property Tees $tees
 * @property Competition $competition
 * @property Golfer $golfer
 * @property Team $team
 * @property Flight $flight
 */
class _Registration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'registration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['competition_id', 'golfer_id', 'status'], 'required'],
            [['competition_id', 'golfer_id', 'team_id', 'flight_id', 'tees_id', 'score', 'score_net', 'points', 'position'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string', 'max' => 20],
            [['note'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('golfleague', 'ID'),
            'competition_id' => Yii::t('golfleague', 'Competition ID'),
            'golfer_id' => Yii::t('golfleague', 'Golfer ID'),
            'status' => Yii::t('golfleague', 'Status'),
            'team_id' => Yii::t('golfleague', 'Team ID'),
            'flight_id' => Yii::t('golfleague', 'Flight ID'),
            'tees_id' => Yii::t('golfleague', 'Tees ID'),
            'note' => Yii::t('golfleague', 'Note'),
            'created_at' => Yii::t('golfleague', 'Created At'),
            'updated_at' => Yii::t('golfleague', 'Updated At'),
            'score' => Yii::t('golfleague', 'Score'),
            'score_net' => Yii::t('golfleague', 'Score Net'),
            'points' => Yii::t('golfleague', 'Points'),
            'position' => Yii::t('golfleague', 'Position'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTees()
    {
        return $this->hasOne(Tees::className(), ['id' => 'tees_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetition()
    {
        return $this->hasOne(Competition::className(), ['id' => 'competition_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGolfer()
    {
        return $this->hasOne(Golfer::className(), ['id' => 'golfer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlight()
    {
        return $this->hasOne(Flight::className(), ['id' => 'flight_id']);
    }
}
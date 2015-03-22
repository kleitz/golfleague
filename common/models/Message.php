<?php

namespace common\models;
use yii\db\ActiveRecord;
use common\components\Constant;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property string $subject
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $body
 * @property string $message_start
 * @property string $message_end
 * @property string $message_type
 */
class Message extends _Message
{
	use Constant;
	
    /** Type */
    const TYPE_BLOG = 'BLOG';
    /** Type */
    const TYPE_ALERT = 'ALERT';
    /** Type */
    const TYPE_REGISTRATION = 'REGISTRATION';
    /** Type */
    const TYPE_RESULT = 'RESULT';
	
    /** Status */
    const STATUS_ACTIVE = 'ACTIVE';
    /** Status */
    const STATUS_CLOSED = 'CLOSED';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
                'timestamp' => [
                        'class' => 'yii\behaviors\TimestampBehavior',
                        'attributes' => [
                                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                                ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                        ],
                        'value' => function() { return date('Y-m-d H:i:s'); /* mysql datetime format is ‘AAAA-MM-JJ HH:MM:SS’*/},
                ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('golfleague', 'Message'),
            'subject' => Yii::t('golfleague', 'Subject'),
            'status' => Yii::t('golfleague', 'Status'),
            'created_at' => Yii::t('golfleague', 'Created At'),
            'updated_at' => Yii::t('golfleague', 'Updated At'),
            'body' => Yii::t('golfleague', 'Body'),
            'message_start' => Yii::t('golfleague', 'Message Start'),
            'message_end' => Yii::t('golfleague', 'Message End'),
            'message_type' => Yii::t('golfleague', 'Message Type'),
        ];
    }

	// just the excerpt
	function first_n_words($number_of_words = 50) {
	   // Where excerpts are concerned, HTML tends to behave
	   // like the proverbial ogre in the china shop, so best to strip that
	   $text = strip_tags($this->body);

	   // \w[\w'-]* allows for any word character (a-zA-Z0-9_) and also contractions
	   // and hyphenated words like 'range-finder' or "it's"
	   // the /s flags means that . matches \n, so this can match multiple lines
	   $text = preg_replace("/^\W*((\w[\w'-]*\b\W*){1,$number_of_words}).*/ms", '\\1', $text);

	   // strip out newline characters from our excerpt
	   return str_replace("\n", "", $text);
	}

	// excerpt plus link if shortened
	function truncate_to_n_words($url, $number_of_words = 50) {
	   $text = strip_tags($this->body);
	   $excerpt = $this->first_n_words($number_of_words);
	   // we can't just look at the length or try == because we strip carriage returns
	   if( str_word_count($text) !== str_word_count($excerpt) ) {
	      $excerpt .= '... <a href="'.$url.'">'.Yii::t('golfleague', 'Read more...').'</a>';
	   }
	   return $excerpt;
	}

}
<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $language_id
 * @property string $nom
 * @property string $cognoms
 * @property string $telefono
 * @property string $email
 * @property string $dni
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $password_reset_token
 * @property int $role
 * @property \yii\db\ActiveQuery $media
 * @property \yii\db\ActiveQuery $language
 * @property string $fullName
 * @property mixed $rolString
 * @property string $urlFotoPerfil
 * @property string $imatge
 *
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * Roles
     */
    const ADMIN = 1;
    const EDITOR = 2;
    const USER = 3;
    const ROLES = [self::ADMIN => 'ADMIN', self::EDITOR => 'EDITOR', self::USER => 'USER'];

    /* camp per a filtrar */
    public $general;

    public $imatge;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'email',
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'nom', 'cognoms', 'language_id'], 'required'],
            [['language_id'], 'string', 'max' => 2],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['role', 'actiu'], 'integer'],
            [['nom', 'cognoms', 'email', 'password'], 'string', 'max' => 250],
            [['telefon'], 'string', 'max' => 50],
            [['username'], 'string', 'max' => 100],
            [['imatge'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 250],
            [['username', 'email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'nom' => Yii::t('app', 'name'),
            'cognoms' => Yii::t('app', 'surname'),
            'telefon' => Yii::t('app', 'phone'),
            'actiu' => Yii::t('app', 'active'),
            'email' => Yii::t('app', 'email'),
            'username' => Yii::t('app', 'username'),
            'password' => Yii::t('app', 'password'),
            'authKey' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
            'role' => Yii::t('app', 'Role'),
            'imatge' => Yii::t('app', 'Imatge'),
            'language_id' => Yii::t('app', 'Language'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // controlem que no mos pugon fer la pirula, nomes un admin pot cambiar estos parametres
        if (!Yii::$app->user->identity->isAdmin()) {
            $this->role = Yii::$app->user->identity->role;
        }

        return true;
    }


    /**
     * Retorna tots els usuaris filtrats segons permisos
     */
    public static function getUsuaris()
    {
        return self::find()->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @param $email
     * @return User|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }


    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 1;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language_id']);
    }

    /**
     * @param $lang_code
     */
    public function changeLanguage($lang_code)
    {
        $session = Yii::$app->session;
        $session->set('language', $lang_code);

        Yii::$app->user->identity->language_id = $lang_code;
        Yii::$app->user->identity->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->nom . ' ' . $this->cognoms;
    }

    /**
     * @return mixed
     */
    public function getRolString()
    {
        return $this::ROLES[$this->role];
    }

    /**
     * @return string
     */
    public function getUrlFotoPerfil()
    {
        return Yii::$app->request->baseUrl . '/images/uploads/users/' . $this->imatge;
    }
}

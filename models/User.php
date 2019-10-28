<?php

namespace app\models;

use Yii;

use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
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
 * @property string $imatge
 *
 */

class User extends \yii\db\ActiveRecord  implements IdentityInterface
{
    /* camp per a filtrar */
    public $general;
    // camp imatge
    public $imatge;
    /* 1, 2, 3 */
    const ROLES = [1 => 'ADMINISTRADOR', 2 => 'GESTOR', 3 => 'USUARI'];
    const ADMIN = 1; CONST GESTOR = 2; CONST USUARI = 3;

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
            [['email'], 'email'],
            [['email'], 'unique'],
            [['role', 'actiu', 'language_id'], 'integer'],
            [['nom', 'cognoms', 'email',  'password'], 'string', 'max' => 250],
            [['telefon'], 'string', 'max' => 50],
            [['username'], 'string', 'max' => 100],
            [['imatge'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 250],
            [['username','email'], 'unique'],
        ];
    }

    // custom before save Josep
    public function beforeSave($insert)
    {
      if (!parent::beforeSave($insert)) {
        return false;
      }
      // controlem que no mos pugon fer la pirula, nomes un admin pot cambiar estos parametres
      if(!Yii::$app->user->identity->esAdmin()){
        $this->role = Yii::$app->user->identity->role;
      }
      return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','id'),
            'nom' => Yii::t('app','name'),
            'cognoms' => Yii::t('app','surname'),
            'telefon' => Yii::t('app','phone'),
            'actiu' => Yii::t('app','active'),
            'email' => Yii::t('app','email'),
            'username' => Yii::t('app','username'),
            'password' => Yii::t('app','password'),
            'authKey' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
            'role' => Yii::t('app','Role'),
            'imatge' => Yii::t('app','Imatge'),
            'language_id' => Yii::t('app','Language'),
        ];
    }

    /**
     * Retorna tots els usuaris filtrats segons permisos
     */
    public static function getUsuaris()
    {
      return self::find()->all();
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
         return $this->hasOne(Language::className(), ['id' => 'language_id']);
     }
 

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModuls()
    {
      return Modul::findAll();
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
        * Finds user by username
        *
        * @param string $username
        * @return static|null
    */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
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
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
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

   // Comproba si es admin retorna bool
   public function esAdmin()
   {
     return $this->role === 1;
   }

   // Comproba si es gestor retorna bool
   public function esGestor()
   {
     return $this->role === 2;
   }

   // Comproba si com a mínim es gestor retorna bool
   public function esMinimGestor()
   {
     return $this->role < 3;
   }

   // Retorne lo nom complet nom + cognoms
   public function getNomComplet()
   {
       return $this->nom . ' ' . $this->cognoms;
   }

    // String nom del rol en funcio de la id
    public function getRolString()
    {
        return $this::ROLES[$this->role];
    }

    // Url foto del perfil
    public function getUrlFotoPerfil()
    {
      return Yii::$app->request->baseUrl . '/images/uploads/users/' . $this->imatge;
    }
}

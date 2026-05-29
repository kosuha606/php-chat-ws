<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель User для таблицы "user"
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string|null $auth_key
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    /**
     * Имя таблицы
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * Правила валидации
     */
    public function rules(): array
    {
        return [
            [['username', 'email', 'password_hash'], 'required'],
            [['username', 'email'], 'unique'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],

            [['username', 'email', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            ['email', 'email'],
        ];
    }

    /**
     * Названия атрибутов
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'email' => 'Email',
            'password_hash' => 'Хэш пароля',
            'auth_key' => 'Ключ авторизации',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    /**
     * Автоматическое заполнение дат
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * Поиск пользователя по ID
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne([
            'id' => $id,
        ]);
    }

    /**
     * Поиск пользователя по access token
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return static::findOne([
            'auth_key' => $token,
        ]);
    }

    /**
     * Поиск по username
     */
    public static function findByUsername(string $username): ?self
    {
        return static::findOne([
            'username' => $username,
        ]);
    }

    /**
     * ID пользователя
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Auth key
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * Проверка auth key
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Проверка пароля
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password_hash
        );
    }

    /**
     * Установка пароля
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерация auth key
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}

<?php


namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallback
 * @ORM\Table(name="user_users", uniqueConstrains={
 *      @ORM\UniqueConstrains(Columns={"email"}),
 *      @ORM\UniqueConstrains(Columns={"reset_token_token"})
 * })
 */
class User
{
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';
    private const STATUS_NEW= 'new';

    /**
     * @var Id
     * @ORM\Column(type="user_user_id")
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;
    /**
     * @var Email
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="password_hash")
     */
    private $passwordHash;
    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, name="confirm_token")
     */
    private $confirmToken;
    /**
     * @var string
     * @ORM\Column(type="string", lenght=16)
     */
    private $status;
    /**
     * @var Role
     * @ORM\Column(type="user_user_role")
     */
    private $role;
    /**
     * @var Network[]\ArrayCollection
     */
    private $networks;

    private function __construct(Id $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(Id $id, \DateTimeImmutable $date, Email $email, string $hash, string $confirmToken): User
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $confirmToken;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed!');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date, string $network, string $identity): User
    {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing){
            if ($existing->isForNetwork($network)){
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
        $this->status = self::STATUS_ACTIVE;
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()){
            throw new \DomainException('User is not active!');
        }
        if (!$this->email){
            throw new \DomainException('Email is not specified!');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)){
            throw new \DomainException('Resetting is already requested!');
        }
        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken){
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)){
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)){
            throw new \DomainException('Role is already same!');
        }
        $this->role = $role;
    }
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()){
            $this->resetToken = null;
        }
    }
}
<?php

namespace Kalibri\Model\Entity;

/**
 * @method int getProfileId()
 * @method string getFirstName()
 * @method string getLastName()
 * @method string getLogin()
 * @method string getPassword()
 * @method int getBirthday()
 * @method int getBanFlag()
 * @method string getBanReason()
 * @method int getRole()
 * @method int getRegisteredAt()
 * @method \Kalibri\Model\Entity\Profile setFirstName($firstName)
 * @method \Kalibri\Model\Entity\Profile setLastName($lastName)
 * @method \Kalibri\Model\Entity\Profile setBirthday($birthDay)
 * @method \Kalibri\Model\Entity\Profile setNickname($nickName)
 * @method \Kalibri\Model\Entity\Profile setLogin($login)
 * @method \Kalibri\Model\Entity\Profile setPassword($password)
 */
class Profile extends \Kalibri\Model\Entity
{
    protected $profileId;
    protected $login;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $birthday;
    protected $banFlag;
    protected $banReason;
    protected $role;
    protected $registeredAt;

    /**
     *  Data initialization
     *
     * @param $data array Row data from db
     */
    public function initData(array $data)
    {
        $this->profileId  = $data['profile_id'];
        $this->login      = $data['login'];
        $this->password   = $data['password'];
        $this->firstName  = $data['first_name'];
        $this->lastName   = $data['last_name'];
        $this->birthday   = $data['birthday'] ? strtotime($data['birthday']) : null;
        $this->banFlag    = (int)$data['ban_flag'];
        $this->banReason  = $data['ban_reason'];
        $this->role       = (int)$data['role'];
        $this->registeredAt = $data['registered_at'] ? strtotime($data['registered_at']): null;
    }

    /**
     *  Get all data as array. Format is $field=>$value
     *
     * @return array
     */
    public function getAllData()
    {
        return array(
            'profile_id' => $this->profileId,
            'login'      => $this->login,
            'password'   => $this->password,
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'birthday'   => date('Y-m-d', $this->birthday),
            'ban_flag'   => (int)$this->banFlag,
            'ban_reason' => $this->banReason,
            'role'       => (int)$this->role,
            'registered_at'=> date('Y-m-d H:i:s', $this->registeredAt)
        );
    }

    /**
     * Alias for getProfileId()
     *
     * @return int
     */
    public function getId()
    {
        return $this->profileId;
    }
}